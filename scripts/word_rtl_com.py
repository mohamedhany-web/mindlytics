# -*- coding: utf-8 -*-
"""إنشاء مستند Word عربي RTL عبر Microsoft Word COM (Windows)."""

from __future__ import annotations

from pathlib import Path

# ثوابت Word
WD_READINGORDER_RTL = 1
WD_ALIGN_RIGHT = 2
WD_STORY = 6  # نهاية المستند — لـ EndKey
WD_PAGE_BREAK = 7
WD_FORMAT_DOCX = 12
WD_LINE_SPACE_MULTIPLE = 5
WD_ALIGN_ROW_RIGHT = 2
WD_TABLE_DIRECTION_RTL = 1

FONT_AR_TITLE = "Traditional Arabic"
FONT_AR_HEADING = "Arabic Typesetting"
FONT_AR_BODY = "Tahoma"
FONT_EN = "Segoe UI"

# ألوان BGR لـ Word
CLR_PRIMARY = 0x8A3A1E
CLR_ACCENT = 0xA87B03
CLR_MUTED = 0x8B7464
CLR_PARTNER = 0x699605
CLR_WHITE = 0xFFFFFF


def _rgb_bgr(r: int, g: int, b: int) -> int:
    return r + (g << 8) + (b << 16)


def _fmt_para(para, *, size: float = 12, bold: bool = False, color: int | None = None, space_after: float = 6):
    pf = para.Range.ParagraphFormat
    pf.ReadingOrder = WD_READINGORDER_RTL
    pf.Alignment = WD_ALIGN_RIGHT
    pf.SpaceAfter = space_after
    pf.LineSpacingRule = WD_LINE_SPACE_MULTIPLE
    pf.LineSpacing = 13.8
    pf.RightIndent = 0
    pf.LeftIndent = 0

    f = para.Range.Font
    f.NameBi = FONT_AR_BODY
    f.SizeBi = size
    f.BoldBi = bold
    f.Name = FONT_EN
    f.Size = size
    f.Bold = bold
    if color is not None:
        f.Color = color


def _rtl_selection(sel):
    sel.ParagraphFormat.ReadingOrder = WD_READINGORDER_RTL
    sel.ParagraphFormat.Alignment = WD_ALIGN_RIGHT
    sel.Font.NameBi = FONT_AR_BODY
    sel.Font.SizeBi = 12


class WordRtlDocument:
    def __init__(self):
        import win32com.client as win32

        self.word = win32.gencache.EnsureDispatch("Word.Application")
        self.word.Visible = False
        self.word.DisplayAlerts = 0
        try:
            self.word.Options.ArabicNumeral = 0  # أرقام عربية ١٢٣
        except Exception:
            pass
        self.doc = self.word.Documents.Add()
        self.sel = self.word.Selection

        # المستند بالكامل RTL
        normal = self.doc.Styles("Normal")
        normal.ParagraphFormat.ReadingOrder = WD_READINGORDER_RTL
        normal.ParagraphFormat.Alignment = WD_ALIGN_RIGHT
        normal.Font.NameBi = FONT_AR_BODY
        normal.Font.SizeBi = 12

        self.doc.Content.ParagraphFormat.ReadingOrder = WD_READINGORDER_RTL
        self.doc.Content.ParagraphFormat.Alignment = WD_ALIGN_RIGHT
        _rtl_selection(self.sel)

    def _go_end(self):
        self.sel.EndKey(Unit=WD_STORY)

    def _last_para(self):
        return self.doc.Paragraphs(self.doc.Paragraphs.Count)

    def page_break(self):
        self._go_end()
        self.sel.InsertBreak(Type=WD_PAGE_BREAK)

    def paragraph(self, text: str, *, size: float = 12, bold: bool = False, color: int | None = None, space_after: float = 6, font_bi: str | None = None):
        self._go_end()
        _rtl_selection(self.sel)
        self.sel.TypeText(text)
        self.sel.TypeParagraph()
        p = self._last_para()
        _fmt_para(p, size=size, bold=bold, color=color, space_after=space_after)
        if font_bi:
            p.Range.Font.NameBi = font_bi

    def heading(self, text: str, level: int = 1):
        cfg = {
            1: (FONT_AR_TITLE, 22, CLR_PRIMARY, 12),
            2: (FONT_AR_HEADING, 17, CLR_ACCENT, 9),
            3: (FONT_AR_HEADING, 14, CLR_PRIMARY, 7),
        }
        font, size, color, sa = cfg.get(level, cfg[3])
        self._go_end()
        _rtl_selection(self.sel)
        self.sel.TypeText(text)
        self.sel.TypeParagraph()
        p = self._last_para()
        _fmt_para(p, size=size, bold=True, color=color, space_after=sa)
        p.Range.Font.NameBi = font

    def label(self, text: str):
        self.paragraph(text, size=11, bold=True, color=CLR_PARTNER, space_after=4)

    def bullets(self, items: list[str]):
        for item in items:
            self._go_end()
            _rtl_selection(self.sel)
            self.sel.TypeText(item)
            self.sel.TypeParagraph()
            p = self._last_para()
            p.Range.ListFormat.ApplyBulletDefault()
            _fmt_para(p, size=11.5, space_after=3)

    def numbered(self, items: list[str]):
        for item in items:
            self._go_end()
            _rtl_selection(self.sel)
            self.sel.TypeText(item)
            self.sel.TypeParagraph()
            p = self._last_para()
            p.Range.ListFormat.ApplyNumberDefault()
            _fmt_para(p, size=11.5, space_after=3)

    def table(self, headers: list[str], rows: list[tuple]):
        self._go_end()
        nrows = 1 + len(rows)
        ncols = len(headers)
        t = self.doc.Tables.Add(self.sel.Range, nrows, ncols)
        t.TableDirection = WD_TABLE_DIRECTION_RTL
        t.Rows.Alignment = WD_ALIGN_ROW_RIGHT

        for c, h in enumerate(headers):
            cell = t.Cell(1, c + 1)
            cell.Range.Text = h
            _fmt_para(cell.Range.Paragraphs(1), size=11, bold=True, color=CLR_WHITE)
            cell.Shading.BackgroundPatternColor = CLR_PRIMARY
            cell.Range.ParagraphFormat.ReadingOrder = WD_READINGORDER_RTL
            cell.Range.ParagraphFormat.Alignment = WD_ALIGN_RIGHT

        for r, row in enumerate(rows, start=2):
            for c, val in enumerate(row):
                cell = t.Cell(r, c + 1)
                cell.Range.Text = val
                _fmt_para(cell.Range.Paragraphs(1), size=10.5)
                cell.Range.ParagraphFormat.ReadingOrder = WD_READINGORDER_RTL
                cell.Range.ParagraphFormat.Alignment = WD_ALIGN_RIGHT

        self.sel.EndKey(Unit=WD_STORY)
        self.sel.TypeParagraph()

    def module(self, mod: dict):
        self.heading(mod["title"], 2)
        if mod.get("subtitle"):
            self.paragraph(mod["subtitle"], size=11, color=CLR_MUTED, space_after=6)
        self.label("ماذا يفعل هذا الجزء؟")
        self.paragraph(mod["summary"], space_after=6)
        self.label("القدرات التفصيلية")
        self.bullets(mod["features"])
        self.label("لماذا يهم الشريك؟")
        self.bullets(mod["partner_value"])
        if mod.get("metrics"):
            self.label("مؤشرات يمكن متابعتها")
            self.bullets(mod["metrics"])
        self.paragraph("", space_after=4)

    def save(self, path: Path):
        path = path.resolve()
        path.parent.mkdir(parents=True, exist_ok=True)
        self.doc.SaveAs2(str(path), FileFormat=WD_FORMAT_DOCX)

    def close(self):
        self.doc.Close(False)
        self.word.Quit()
