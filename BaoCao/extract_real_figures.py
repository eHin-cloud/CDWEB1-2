import docx, re, sys
sys.stdout.reconfigure(encoding="utf-8")

doc = docx.Document("Báo cáo nhóm A_fixed.docx")
real_figures = []

for i, p in enumerate(doc.paragraphs[100:]):
    t = p.text.strip()
    if t.startswith("Hình ") and not t.endswith("\t25") and not t.endswith("\t39"):
        real_figures.append((100+i, t))

print(f"Total real figures in doc: {len(real_figures)}")
for idx, fig in real_figures:
    print(f"P{idx}: {fig}")
