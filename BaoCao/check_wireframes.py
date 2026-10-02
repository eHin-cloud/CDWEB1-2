# -*- coding: utf-8 -*-
import sys
import docx

sys.stdout.reconfigure(encoding='utf-8')

for name in ['Báo cáo nhóm A.docx', 'BaoCao_GiaoDien_Message_30ChucNang (1).docx', 'BaoCao_MoiNHat.docx', 'BaoCao_NhomA_backup_original.docx']:
    print('=============================================')
    print('FILE:', name)
    try:
        doc = docx.Document(name)
    except Exception as e:
        print('Lỗi mở file:', e)
        continue
    wireframes = []
    actual_uis = []
    for i, p in enumerate(doc.paragraphs):
        txt = p.text.strip()
        if 'phác thảo' in txt.lower() or 'wireframe' in txt.lower():
            wireframes.append((i, txt))
        elif 'hình ' in txt.lower() and any(c.isdigit() for c in txt):
            actual_uis.append((i, txt))
    print(f"Số lượng caption chứa 'phác thảo / wireframe': {len(wireframes)}")
    for idx, (p_idx, t) in enumerate(wireframes, 1):
        print(f"  {idx:2d}. P[{p_idx}]: {t[:80]}")
    print(f"Số lượng caption hình khác: {len(actual_uis)}")
