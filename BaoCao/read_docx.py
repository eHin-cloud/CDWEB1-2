#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script đọc và trích xuất nội dung từ tệp Word (.docx)
Không cần cài thêm thư viện bên ngoài (sử dụng thư viện chuẩn của Python: zipfile, xml.etree).
Có thể tái sử dụng cho bất kỳ tệp .docx nào.

Cách dùng:
    python read_docx.py [duong_dan_file.docx] [--output file_xuat.md] [--headings-only]
"""

import sys
import os
import zipfile
import xml.etree.ElementTree as ET
import argparse
from pathlib import Path

# Đảm bảo stdout/stderr hỗ trợ UTF-8 trên console Windows
if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8', errors='replace')
if hasattr(sys.stderr, 'reconfigure'):
    sys.stderr.reconfigure(encoding='utf-8', errors='replace')


# Các namespace chuẩn trong cấu trúc OpenXML của Word
NAMESPACES = {
    'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main',
    'r': 'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
    'm': 'http://schemas.openxmlformats.org/officeDocument/2006/math',
    'v': 'urn:schemas-microsoft-com:vml',
    'wp': 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing',
    'w10': 'urn:schemas-microsoft-com:office:word',
}


def extract_docx_content(docx_path):
    """
    Trích xuất toàn bộ cấu trúc văn bản từ tệp .docx bao gồm tiêu đề, đoạn văn và bảng biểu.
    Trả về: danh sách các phần tử {'type': 'p'|'heading'|'table', 'level': int, 'text': str, 'rows': list}
    """
    docx_path = Path(docx_path)
    if not docx_path.exists():
        raise FileNotFoundError(f"Không tìm thấy tệp: {docx_path}")

    elements = []

    with zipfile.ZipFile(docx_path, 'r') as docx_zip:
        xml_content = docx_zip.read('word/document.xml')
        root = ET.fromstring(xml_content)

        # Lấy phần body
        body = root.find('w:body', NAMESPACES)
        if body is None:
            return elements

        for child in body:
            tag_name = child.tag.split('}')[-1]

            if tag_name == 'p':
                # Paragraph hoặc Heading
                p_text, heading_level = parse_paragraph(child)
                if p_text:
                    if heading_level > 0:
                        elements.append({
                            'type': 'heading',
                            'level': heading_level,
                            'text': p_text
                        })
                    else:
                        elements.append({
                            'type': 'p',
                            'level': 0,
                            'text': p_text
                        })

            elif tag_name == 'tbl':
                # Bảng biểu
                table_rows = parse_table(child)
                if table_rows:
                    elements.append({
                        'type': 'table',
                        'rows': table_rows
                    })

    return elements


def parse_paragraph(p_elem):
    """Lấy nội dung văn bản và xác định cấp độ heading (nếu có)."""
    # Kiểm tra kiểu dáng (pStyle) để phát hiện Heading
    heading_level = 0
    p_pr = p_elem.find('w:pPr', NAMESPACES)
    if p_pr is not None:
        p_style = p_pr.find('w:pStyle', NAMESPACES)
        if p_style is not None:
            val = p_style.attrib.get(f"{{{NAMESPACES['w']}}}val", "")
            val_lower = val.lower()
            if 'heading' in val_lower or 'tiêu đề' in val_lower or 'tieude' in val_lower:
                for ch in val:
                    if ch.isdigit():
                        heading_level = int(ch)
                        break
                if heading_level == 0:
                    heading_level = 1

    # Gom toàn bộ văn bản trong các thẻ w:t
    texts = []
    for t_elem in p_elem.findall('.//w:t', NAMESPACES):
        if t_elem.text:
            texts.append(t_elem.text)

    full_text = "".join(texts).strip()
    return full_text, heading_level


def parse_table(tbl_elem):
    """Trích xuất dữ liệu từ bảng biểu."""
    rows = []
    for tr in tbl_elem.findall('w:tr', NAMESPACES):
        row_cells = []
        for tc in tr.findall('w:tc', NAMESPACES):
            cell_texts = []
            for t in tc.findall('.//w:t', NAMESPACES):
                if t.text:
                    cell_texts.append(t.text)
            row_cells.append(" ".join("".join(cell_texts).split()))
        if any(row_cells):
            rows.append(row_cells)
    return rows


def elements_to_markdown(elements):
    """Chuyển đổi các phần tử trích xuất thành định dạng Markdown."""
    lines = []
    for elem in elements:
        if elem['type'] == 'heading':
            level = min(max(elem['level'], 1), 6)
            lines.append(f"\n{'#' * level} {elem['text']}\n")
        elif elem['type'] == 'p':
            lines.append(f"{elem['text']}\n")
        elif elem['type'] == 'table':
            rows = elem['rows']
            if not rows:
                continue
            # Header
            header = rows[0]
            lines.append("| " + " | ".join(header) + " |")
            lines.append("| " + " | ".join(["---"] * len(header)) + " |")
            # Rows
            for row in rows[1:]:
                # Cân bằng số cột
                padded_row = row + [""] * (len(header) - len(row))
                lines.append("| " + " | ".join(padded_row[:len(header)]) + " |")
            lines.append("")
    return "\n".join(lines)


def get_outline(elements):
    """Trích xuất mục lục / dàn ý từ danh sách phần tử."""
    outline = []
    for elem in elements:
        if elem['type'] == 'heading':
            indent = "  " * (elem['level'] - 1)
            outline.append(f"{indent}- {elem['text']}")
    return "\n".join(outline)


def main():
    parser = argparse.ArgumentParser(
        description="Đọc và trích xuất nội dung tệp .docx không cần thư viện ngoài."
    )
    # Mặc định file trong cùng thư mục
    default_doc = Path(__file__).resolve().parent / "baocaomoi.docx"

    parser.add_argument(
        "file",
        nargs="?",
        default=str(default_doc),
        help=f"Đường dẫn tới file .docx (Mặc định: {default_doc.name})"
    )
    parser.add_argument(
        "-o", "--output",
        help="Đường dẫn file để lưu kết quả (.md hoặc .txt). Nếu không chỉ định sẽ in ra console."
    )
    parser.add_argument(
        "--headings-only",
        action="store_true",
        help="Chỉ in ra dàn ý / tiêu đề (Headings) của tài liệu."
    )

    args = parser.parse_args()
    doc_path = Path(args.file).resolve()

    if not doc_path.exists():
        print(f"[LỖI] Không tìm thấy file: {doc_path}", file=sys.stderr)
        sys.exit(1)

    try:
        elements = extract_docx_content(doc_path)
    except Exception as e:
        print(f"[LỖI] Không thể đọc file .docx: {e}", file=sys.stderr)
        sys.exit(1)

    if args.headings_only:
        outline = get_outline(elements)
        if outline:
            output_content = outline
        else:
            output_content = "Không phát hiện thấy Heading dạng chuẩn. Thử xuất toàn bộ tài liệu."
    else:
        output_content = elements_to_markdown(elements)

    if args.output:
        out_file = Path(args.output).resolve()
        out_file.write_text(output_content, encoding="utf-8")
        print(f"[THÀNH CÔNG] Đã lưu nội dung vào: {out_file}")
    else:
        # In ra màn hình console (UTF-8)
        try:
            print(output_content)
        except UnicodeEncodeError:
            # Phòng ngừa console Windows cp1252 / cp437
            sys.stdout.buffer.write(output_content.encode('utf-8'))
            print()


if __name__ == "__main__":
    main()
