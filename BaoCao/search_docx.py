#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script tìm kiếm từ khóa nhanh trong tệp Word (.docx)
Không cần cài thư viện ngoài.

Cách dùng:
    python search_docx.py <tu_khoa> [duong_dan_file.docx]
Ví dụ:
    python search_docx.py "VietQR"
    python search_docx.py "WebAuthn" baocaomoi.docx
"""

import sys
from pathlib import Path
from read_docx import extract_docx_content

# Đảm bảo UTF-8 cho Windows Console
if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8', errors='replace')
if hasattr(sys.stderr, 'reconfigure'):
    sys.stderr.reconfigure(encoding='utf-8', errors='replace')


def search_in_elements(elements, keyword):
    keyword_lower = keyword.lower()
    matches = []

    for idx, elem in enumerate(elements):
        if elem['type'] in ('p', 'heading'):
            text = elem['text']
            if keyword_lower in text.lower():
                matches.append({
                    'type': elem['type'],
                    'content': text
                })
        elif elem['type'] == 'table':
            for row_idx, row in enumerate(elem['rows']):
                row_str = " | ".join(row)
                if keyword_lower in row_str.lower():
                    matches.append({
                        'type': f"table_row (Dòng {row_idx + 1})",
                        'content': row_str
                    })

    return matches


def main():
    if len(sys.argv) < 2:
        print("Cách dùng: python search_docx.py <tu_khoa> [file.docx]")
        print("Ví dụ:    python search_docx.py \"VietQR\"")
        sys.exit(1)

    keyword = sys.argv[1]
    default_doc = Path(__file__).resolve().parent / "baocaomoi.docx"
    doc_path = Path(sys.argv[2]).resolve() if len(sys.argv) > 2 else default_doc

    if not doc_path.exists():
        print(f"[LỖI] Không tìm thấy tệp: {doc_path}")
        sys.exit(1)

    print(f"[*] Đang tìm kiếm từ khóa '{keyword}' trong: {doc_path.name} ...\n")
    elements = extract_docx_content(doc_path)
    results = search_in_elements(elements, keyword)

    print(f"[+] Tìm thấy {len(results)} kết quả phù hợp:\n" + "=" * 60)
    for i, res in enumerate(results, 1):
        print(f"[{i}] [{res['type']}]")
        print(f"    {res['content']}\n")


if __name__ == "__main__":
    main()
