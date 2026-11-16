import docx
import json
import re

doc = docx.Document('BaoCao/NhomC_baocao_2026102.docx')
print(f'Total tables: {len(doc.tables)}')

features_data = {}

for fn in range(1, 31):
    f_info = {'num': fn, 'info_rows': [], 'err_rows': []}
    
    pat_feat = re.compile(rf'FEAT_{fn:02d}(_|\b)|FEAT_{fn}(_|\b)', re.IGNORECASE)
    pat_err = re.compile(rf'ERR_{fn:02d}(_|\b)|ERR_{fn}(_|\b)', re.IGNORECASE)
    
    for i, t in enumerate(doc.tables):
        full_text = ' '.join(c.text for r in t.rows for c in r.cells)
        if pat_feat.search(full_text):
            rows = []
            for r in t.rows:
                cells = [c.text.strip().replace('\n', ' ') for c in r.cells]
                rows.append(cells)
            f_info['info_rows'] = rows
        elif pat_err.search(full_text):
            rows = []
            for r in t.rows:
                cells = [c.text.strip().replace('\n', ' ') for c in r.cells]
                rows.append(cells)
            f_info['err_rows'] = rows
                
    features_data[fn] = f_info
    print(f"Feature {fn:02d}: {len(f_info['info_rows'])} info_rows, {len(f_info['err_rows'])} err_rows")

with open('features_extracted.json', 'w', encoding='utf-8') as f:
    json.dump(features_data, f, ensure_ascii=False, indent=2)
print('Saved to features_extracted.json')
