"""Builds docs/diagrams/noor-store-algorithms.drawio (academic multi-page draw.io file).

Run:  python docs/diagrams/build_drawio.py
"""
from pathlib import Path
from xml.sax.saxutils import quoteattr

FONT = "fontFamily=Tahoma;"
BOX = "rounded=1;whiteSpace=wrap;html=1;arcSize=14;fontSize=16;spacing=8;" + FONT


def fill(f, s, extra=""):
    return f"{BOX}fillColor={f};strokeColor={s};strokeWidth=2;{extra}"


ST = {
    "title": "text;html=1;fontSize=28;fontStyle=1;fontColor=#6A1B4D;align=center;verticalAlign=middle;" + FONT,
    "sub": "text;html=1;fontSize=16;fontColor=#555555;align=center;verticalAlign=middle;" + FONT,
    "foot": "text;html=1;fontSize=12;fontColor=#999999;align=center;verticalAlign=middle;" + FONT,
    "blue": fill("#DAE8FC", "#6C8EBF"),
    "blueH": fill("#6C8EBF", "#6C8EBF", "fontColor=#FFFFFF;fontStyle=1;fontSize=18;"),
    "orange": fill("#FFE6CC", "#D79B00"),
    "orangeH": fill("#D79B00", "#D79B00", "fontColor=#FFFFFF;fontStyle=1;fontSize=18;"),
    "green": fill("#D5E8D4", "#82B366"),
    "greenH": fill("#82B366", "#82B366", "fontColor=#FFFFFF;fontStyle=1;fontSize=18;"),
    "rose": fill("#F8D7E3", "#D46A92"),
    "plum": fill("#6A1B4D", "#6A1B4D", "fontColor=#FFFFFF;fontStyle=1;fontSize=18;"),
    "gray": fill("#F5F5F5", "#888888"),
    "white": fill("#FFFFFF", "#6A1B4D"),
    "note": "shape=note;whiteSpace=wrap;html=1;size=18;fillColor=#FFF2CC;strokeColor=#D6B656;strokeWidth=2;"
            "fontSize=15;align=center;verticalAlign=middle;spacing=10;" + FONT,
    "actor": "shape=umlActor;verticalLabelPosition=bottom;verticalAlign=top;html=1;fontSize=16;"
             "fillColor=#F8D7E3;strokeColor=#D46A92;strokeWidth=2;" + FONT,
    "bar": "rounded=1;whiteSpace=wrap;html=1;strokeColor=none;",
    "barlabel": "text;html=1;fontSize=15;fontStyle=1;align=left;verticalAlign=middle;" + FONT,
    "barvalue": "text;html=1;fontSize=16;fontStyle=1;align=left;verticalAlign=middle;" + FONT,
    "small": fill("#FFFFFF", "#AAAAAA", "fontSize=14;"),
}

EDGE = ("edgeStyle=orthogonalEdgeStyle;rounded=1;html=1;endArrow=block;endFill=1;strokeWidth=3;"
        "strokeColor=#6A1B4D;fontSize=14;labelBackgroundColor=#FFFFFF;" + FONT)

FOOTER = "Noor-Store · ورقة بحثية — تحسين تجربة المستخدم باستخدام أنظمة التوصية"


def rtl(text):
    return '<div dir="rtl">' + text.replace("\n", "<br>") + "</div>"


class Page:
    def __init__(self, name, title, subtitle=""):
        self.name = name
        self.cells = []
        self.n = 1
        self.box(title, 40, 15, 1520, 50, ST["title"])
        if subtitle:
            self.box(subtitle, 40, 65, 1520, 35, ST["sub"])
        self.box(FOOTER, 40, 865, 1520, 25, ST["foot"])

    def _id(self):
        self.n += 1
        return f"c{self.n}"

    def box(self, text, x, y, w, h, style):
        i = self._id()
        value = rtl(text)
        self.cells.append(
            f'<mxCell id="{i}" value={quoteattr(value)} style={quoteattr(style)} vertex="1" parent="1">'
            f'<mxGeometry x="{x}" y="{y}" width="{w}" height="{h}" as="geometry"/></mxCell>'
        )
        return i

    def edge(self, s, t, label="", style=EDGE, points=None):
        i = self._id()
        pts = ""
        if points:
            pts = '<Array as="points">' + "".join(f'<mxPoint x="{x}" y="{y}"/>' for x, y in points) + "</Array>"
        self.cells.append(
            f'<mxCell id="{i}" value={quoteattr(rtl(label) if label else "")} style={quoteattr(style)} edge="1" parent="1" '
            f'source="{s}" target="{t}"><mxGeometry relative="1" as="geometry">{pts}</mxGeometry></mxCell>'
        )
        return i

    def chain(self, ids, style=EDGE):
        for a, b in zip(ids, ids[1:]):
            self.edge(a, b, "", style)

    def xml(self, idx):
        return (
            f'<diagram id="page{idx}" name={quoteattr(self.name)}>'
            '<mxGraphModel dx="1600" dy="900" grid="0" gridSize="10" guides="1" tooltips="1" connect="1" '
            'arrows="1" fold="1" page="1" pageScale="1" pageWidth="1600" pageHeight="900" math="0" shadow="0">'
            '<root><mxCell id="0"/><mxCell id="1" parent="0"/>' + "".join(self.cells) + "</root></mxGraphModel></diagram>"
        )


def dataset():
    p = Page("1. البيانات والمنهجية", "منهجية البحث وبيانات المتجر",
             "دراسة تطبيقية على متجر مستحضرات تجميل — Noor-Store")
    stats = [
        ("914", "منتج"),
        ("500", "زبون"),
        ("2,778", "طلب"),
        ("2,626", "سلة مكتملة"),
        ("20,411", "مشاهدة"),
        ("1,836", "تقييم"),
    ]
    for i, (n, lab) in enumerate(stats):
        x = 80 + i * 250
        p.box(f"<b style='font-size:26px'>{n}</b>\n{lab}", x, 120, 230, 90, ST["plum"])

    p.box("<b>قصة البحث</b>\nفي متاجر التجميل الزبون غالباً يبحث عن روتين كامل (أحمر شفاه + محدد + أحمر خدود)،\n"
          "ولا يكتفي بمنتج واحد. الهدف الأكاديمي: هل أنظمة التوصية تحسّن تجربة الشراء فعلاً؟\n"
          "طبقنا ثلاث خوارزميات كلاسيكية، وربطناها بواجهة المتجر، ثم قيّمناها رقمياً.",
          80, 240, 760, 200, ST["note"])

    p.box("<b>تصميم التقييم (Hold-out)</b>\n1. أخفينا آخر طلب لكل زبون (488 سلة اختبار)\n"
          "2. درّبنا الخوارزميات على الطلبات الأقدم (2,138 سلة)\n"
          "3. سألنا: هل المنتج المخفي ظهر ضمن أول 5 اقتراحات؟\n"
          "4. قارنّا مع خط أساس: الأكثر مبيعاً + اقتراح عشوائي",
          880, 240, 640, 200, ST["gray"])

    p.box("<b>مقاييس الأداء المستخدمة</b>\n"
          "• HitRate@5 — نسبة المرات التي ظهر فيها المنتج الصحيح ضمن أول 5\n"
          "• MRR — متوسط ترتيب الظهور الأول (كلما أقرب للمرتبة 1 كان أفضل)\n"
          "• Precision / Recall — دقة واكتمال التوصيات",
          80, 470, 760, 180, ST["blue"])

    p.box("<b>لماذا هذا التصميم؟</b>\n"
          "إخفاء آخر طلب يحاكي سؤالاً واقعياً:\n"
          "«لو الزبون لم يشترِ بعد، هل النظام كان سيقترح له ما سيشتريه فعلاً؟»\n"
          "هذا معيار شائع في أدبيات أنظمة التوصية.",
          880, 470, 640, 180, ST["white"])

    p.box("البيانات مستخرجة من كتالوج تجميل حقيقي + سلوك شراء مُحاكى لشخصيات زبائن متنوعة",
          80, 680, 1440, 55, ST["rose"])
    return p


def overview():
    p = Page("2. الفكرة العامة", "ثلاث خوارزميات توصية — إطار مفاهيمي",
             "كل خوارزمية تجيب على سؤال بحثي مختلف مرتبط بتجربة المستخدم")
    cols = [
        ("blueH", "blue", "البيع المتقاطع\nCross-selling",
         "سؤال البحث:\nما المنتجات التي تُشترى معاً؟",
         "Apriori\nقواعد الارتباط من سلال الشراء\n+ تهجين بالتصفية التعاونية",
         "يُشترى معاً · أكمل إطلالتك\nتغطية 100% من المنتجات",
         "يكمل الزبون روتينه\nويزيد متوسط حجم السلة"),
        ("orangeH", "orange", "البيع الترقوي\nUpselling",
         "سؤال البحث:\nما البديل الأفضل سعراً وجودة؟",
         "تشابه المحتوى (TF-IDF)\n+ درجة منفعة متعددة المعايير\n+ متوسط بايزي للتقييم",
         "نسخة أفضل · استبدال بالسلة\nتغطية 88.9% من المنتجات",
         "قرار شراء أوضح\nبدون مقارنة يدوية طويلة"),
        ("greenH", "green", "التوصيات الشخصية\nPersonalisation",
         "سؤال البحث:\nما يناسب ذوق هذا الزبون؟",
         "Item-based CF\n(Sarwar et al., 2001)\nمن سلوك زبائن متشابهين",
         "مختار لك · زبائن أعجبهم أيضاً\nتغطية CF ≈ 94.5%",
         "صفحة رئيسية مخصصة\nتقلل وقت البحث"),
    ]
    labels = ["سؤال البحث", "المنهج", "التطبيق في المتجر", "أثر UX المتوقع"]
    for r, (lab, y) in enumerate(zip(labels, [200, 320, 480, 620])):
        p.box(lab, 1340, y, 220, 70 if r != 1 else 120, ST["gray"] + "fontStyle=1;fontSize=14;")
    for i, (head, body, h, q, algo, ui, ux) in enumerate(cols):
        x = 1300 - (i + 1) * 410
        ids = [
            p.box(h, x, 115, 380, 65, ST[head]),
            p.box(q, x, 200, 380, 70, ST["rose"]),
            p.box(algo, x, 320, 380, 120, ST[body]),
            p.box(ui, x, 480, 380, 90, ST["white"]),
            p.box(ux, x, 620, 380, 90, ST["note"]),
        ]
        p.chain(ids)
    p.box("الفرضية: دمج الخوارزميات الثلاث في رحلة الزبون يحسّن دقة الاقتراح مقارنةً بعرض «الأكثر مبيعاً» وحده",
          80, 750, 1440, 55, ST["plum"])
    return p


def system_flow():
    p = Page("3. Flow النظام", "بنية النظام: تدريب غير متصل + خدمة فورية",
             "الفصل بين الحساب الثقيل (Offline) والعرض السريع (Online) معيار تصميمي لتحسين UX")
    a = p.box("<b>1. جمع الإشارات</b>\nطلبات · مشاهدات\nتقييمات · مفضلة\n(20,924 حدث تفاعل)", 1170, 140, 340, 150, ST["gray"])
    b = p.box("<b>2. تدريب دوري Offline</b>\nApriori + Upsell + Item-CF\nعلى كامل البيانات", 640, 140, 340, 150, ST["plum"])
    c = p.box("<b>3. تخزين النتائج</b>\n339 قاعدة منتج\n22 قاعدة نوع\nآلاف توصيات مرتبة مسبقاً", 110, 140, 340, 150, ST["gray"])
    d = p.box("<b>4. عرض Online</b>\nاستعلام سريع عند فتح الصفحة\nبدون إعادة حساب", 110, 480, 340, 150, ST["rose"])
    e = p.box("<b>5. تفاعل الزبون</b>\nنقر · إضافة سلة · شراء\nيُسجَّل كتغذية راجعة", 640, 480, 340, 150, ST["rose"])
    f = p.box("<b>6. تقييم علمي</b>\nHitRate · MRR\nمقارنة مع خطوط أساس", 1170, 480, 340, 150, ST["note"])
    p.edge(a, b)
    p.edge(b, c)
    p.edge(c, d)
    p.edge(d, e)
    p.edge(e, f)
    p.edge(f, a, "بيانات جديدة")
    p.box("<b>لماذا Offline؟</b>\nحساب Apriori والتصفية التعاونية مكلف.\nبحسابه مسبقاً تبقى الصفحة سريعة،\nوهذا شرط أساسي لتجربة مستخدم جيدة.",
          640, 320, 340, 130, ST["note"])
    return p


def cross_sell():
    p = Page("4. Cross-selling", "البيع المتقاطع — Apriori متعدد المستويات",
             "الفكرة الأكاديمية: قواعد الارتباط (Association Rules) من سلال الشراء")
    steps = [
        ("<b>1. سلال الشراء</b>\n2,626 سلة مكتملة = معاملات", "gray"),
        ("<b>2. مجموعات متكررة</b>\nحد أدنى للدعم: 0.3% للمنتجات · 1% للأنواع", "blue"),
        ("<b>3. توليد القواعد</b>\nثقة ≥ 10% ورفع (Lift) > 1", "blue"),
        ("<b>4. تهجين</b>\nإن نقصت القواعد ← نكمل من الجيران التعاونيين", "blue"),
        ("<b>5. الواجهة</b>\n«يُشترى معاً» و«أكمل إطلالتك»", "rose"),
    ]
    ids = [p.box(t, 900, 115 + i * 135, 600, 110, ST[s]) for i, (t, s) in enumerate(steps)]
    p.chain(ids)

    p.box("قصة رقمية من البيانات الحقيقية", 80, 115, 760, 45, ST["blueH"])
    p.box("<b>القاعدة:</b> محدد الشفاه ← أحمر الشفاه", 80, 175, 760, 50, ST["white"])
    p.box("من أصل 393 سلة تحتوي محدد شفاه\n368 منها احتوت أيضاً أحمر شفاه", 80, 245, 760, 80, ST["white"])
    p.box("<b>الثقة Confidence = 93.6%</b>\nأي أن احتمال شراء أحمر الشفاه بعد المحدد عالٍ جداً", 80, 345, 760, 80, ST["note"])
    p.box("<b>الرفع Lift = 2.80</b>\nالعلاقة أقوى بنحو 3 أضعاف من الصدفة\n(لو كانت المنتجات مستقلة لكان الرفع = 1)", 80, 445, 760, 95, ST["blue"])
    p.box("<b>قاعدة أخرى:</b> أحمر خدود ↔ برونزر · Lift = 3.02\n"
          "النتيجة المستخرجة: 339 قاعدة منتج + 22 قاعدة نوع",
          80, 560, 760, 100, ST["gray"])
    p.box("<b>التهجين:</b> قواعد المنتج وحدها تغطي ≈14% فقط. بعد إضافة قواعد النوع والجيران التعاونيين تصبح التغطية 100%.",
          80, 685, 760, 85, ST["rose"])
    return p


def upsell():
    p = Page("5. Upselling", "البيع الترقوي — تشابه المحتوى + منفعة متعددة المعايير",
             "الهدف: اقتراح بديل من نفس النوع أغلى قليلاً وبتقييم أفضل — وليس منتجاً مكمّلاً")
    pin = p.box("الزبون يشاهد منتجاً", 580, 115, 420, 55, ST["rose"])
    hdr = p.box("قيود الترقية (Hard constraints)", 1150, 200, 360, 50, ST["orangeH"])
    f1 = p.box("<b>نفس النوع</b>\nمحدد ← محدد (بديل لا مكمّل)", 1150, 275, 360, 85, ST["orange"])
    f2 = p.box("<b>سعر أعلى 5%–100%</b>\nمثالي حوالي +35%", 1150, 390, 360, 85, ST["orange"])
    f3 = p.box("<b>تقييم بايزي ≥ الحالي</b>\nيمنع منتجاً بتقييم واحد خادع", 1150, 505, 360, 95, ST["orange"])
    rank = p.box("<b>درجة المنفعة (أوزان)</b>\nتشابه الوصف 40%\nجودة التقييم 25%\nملاءمة السعر 20%\nالشعبية 15%",
                 580, 300, 420, 180, ST["orange"])
    out = p.box("<b>العرض</b>\n«نسخة أفضل» + زر استبدال\nتغطية 88.9% · متوسط رفع السعر 31.6%\nمتوسط تحسّن التقييم +0.22 نجمة",
                580, 520, 420, 140, ST["rose"])
    p.edge(pin, hdr, "", EDGE + "exitX=1;exitY=0.5;", [(1330, 142)])
    p.chain([hdr, f1, f2, f3])
    p.edge(f3, rank, "", EDGE + "exitX=0;exitY=0.5;entryX=1;entryY=0.5;")
    p.edge(rank, out)

    p.box("<b>قصة تطبيقية</b>\nسارة تنظر إلى محدد Maybelline بـ 8.29$\nوتقييم بايزي ≈ 3.85\n\n"
          "النظام يقترح Revlon ColorStay بـ 11.49$\n(+38.6%) وتقييم ≈ 4.63\n\n"
          "تشابه الوصف كافٍ، والسعر ضمن النطاق المثالي، والتقييم أعلى بوضوح.",
          80, 200, 450, 320, ST["note"])
    p.box("<b>لماذا ليس Apriori؟</b>\nالزبون لا يشتري المنتج ونسخته الأفضل معاً في سلة واحدة؛\n"
          "المطلوب مقارنة بدائل ضمن نفس الصنف، لذا اعتمدنا المحتوى والسعر والتقييم.",
          80, 550, 450, 180, ST["gray"])
    return p


def personalization():
    p = Page("6. Personalisation", "التوصيات الشخصية — Item-based Collaborative Filtering",
             "المرجع: Sarwar et al. (2001) — التشابه بين المنتجات عبر سلوك المستخدمين المشتركين")
    p.box("سارة", 90, 130, 45, 85, ST["actor"])
    s1 = p.box("شاهدت محدداً واشترت أحمر شفاه", 200, 140, 340, 80, ST["rose"])
    p.box("", 90, 280, 45, 85, ST["actor"])
    s2 = p.box("زبائن بنفس المشتريات\nاشتروا أيضاً أحمر خدود", 200, 280, 340, 90, ST["gray"])
    sim = p.box("النظام يحسب تشابه المنتجات\nمن الأعمدة المشتركة في مصفوفة المستخدم–المنتج", 200, 420, 340, 100, ST["note"])
    rec = p.box("يقترح على سارة أحمر الخدود\n(HitRate@5 = 22.3%)", 200, 560, 340, 90, ST["green"])
    p.edge(s1, sim, "", EDGE + "exitX=1;exitY=0.5;entryX=1;entryY=0.25;", [(580, 180), (580, 445)])
    p.edge(s2, sim)
    p.edge(sim, rec)

    steps = [
        ("<b>1. أوزان الإشارات الضمنية</b>\nشراء = 3 · مفضلة = 2 · مشاهدة = 1", "green"),
        ("<b>2. اضمحلال زمني</b>\nنصف عمر 30 يوماً: الاهتمام الحديث أهم", "green"),
        ("<b>3. تشابه المنتجات</b>\nجيب التمام + تخفيف للأزواج قليلة الدعم", "green"),
        ("<b>4. استبعاد المشتريات السابقة</b>\nلا نعيد اقتراح ما اشتراه الزبون", "green"),
        ("<b>5. الواجهة</b>\n«مختار لك» و«زبائن أعجبهم أيضاً»", "rose"),
    ]
    ids = [p.box(t, 860, 115 + i * 125, 650, 100, ST[s]) for i, (t, s) in enumerate(steps)]
    p.chain(ids)
    p.box("زائر جديد (Cold start): لا تاريخ → نعرض الأكثر رواجاً لآخر 30 يوماً",
          200, 700, 1310, 55, ST["note"])
    return p


def journey():
    p = Page("7. رحلة الزبون", "دمج الخوارزميات في رحلة الشراء",
             "كل مرحلة من الرحلة تستدعي خوارزمية مختلفة لسؤال قرار مختلف")
    p.box("سارة", 50, 115, 40, 80, ST["actor"])
    stages = [
        ("الرئيسية", [("مختار لك\n(Item-CF)", "green"), ("الأكثر رواجاً\n(Cold start)", "gray")]),
        ("صفحة المنتج", [("نسخة أفضل\n(Upsell)", "orange"), ("يُشترى معاً\n(Apriori)", "blue"),
                         ("زبائن أعجبهم أيضاً\n(CF)", "green")]),
        ("السلة", [("استبدال بنسخة أفضل", "orange"), ("أكمل إطلالتك", "blue"),
                   ("متبقي للشحن المجاني", "rose")]),
        ("إتمام الشراء", [("بيانات معبأة مسبقاً\nطلب أسرع", "gray")]),
    ]
    xs = [1180, 830, 480, 130]
    heads = []
    for (name, widgets), x in zip(stages, xs):
        heads.append(p.box(name, x, 120, 300, 55, ST["plum"]))
        y = 210
        for txt, style in widgets:
            p.box(txt, x, y, 300, 80, ST[style])
            y += 95
    p.chain(heads)
    p.box("<b>قصة رحلة سارة:</b> دخلت من «مختار لك» → فتحت أحمر شفاه → رأت محدداً في «يُشترى معاً» "
          "ونسخة أغلى بتقييم أعلى → أضافت الاثنين وأكملت الشحن المجاني من اقتراح السلة.",
          130, 560, 1350, 70, ST["note"])
    legend = [("Apriori Cross-sell", "blue"), ("Upsell", "orange"), ("Item-CF", "green"), ("تحسين واجهة", "rose")]
    for i, (txt, style) in enumerate(legend):
        p.box(txt, 1180 - i * 350, 680, 300, 50, ST[style])
    p.box("بعد الشراء تُسجَّل التوصية التي ساعدت — تغذية راجعة لتحسين الدورة التالية",
          130, 760, 1350, 50, ST["gray"])
    return p


def evaluation():
    p = Page("8. التقييم", "نتائج التقييم التجريبي — Hold-out على آخر طلب",
             "اختبار على 488 سلة مخفية · تدريب على 2,138 سلة · المقياس الأساسي HitRate@5")
    t1 = p.box("تدريب على\nالطلبات الأقدم", 1180, 115, 300, 75, ST["gray"])
    t2 = p.box("إخفاء\nآخر طلب", 830, 115, 300, 75, ST["rose"])
    t3 = p.box("هل ظهر المنتج\nضمن Top-5؟", 480, 115, 300, 75, ST["note"])
    p.chain([t1, t2, t3])

    def chart(title, rows, y0):
        p.box(title, 80, y0, 1440, 40, ST["plum"])
        y = y0 + 55
        for name, v, color in rows:
            w = int(v * 1000 + 0.5)
            p.box(name, 1150, y, 370, 40, ST["barlabel"])
            p.box("", 1140 - w, y + 2, w, 36, ST["bar"] + f"fillColor={color};")
            p.box(f"{int(v * 100 + 0.5)}%", 1140 - w - 85, y, 75, 40, ST["barvalue"].replace("align=left", "align=right"))
            y += 48
        return y

    y = chart("إكمال السلة (Cross-selling) — هل المنتج المكمل ضمن أول 5؟", [
        ("Hybrid Apriori+CF", 0.593, "#6C8EBF"),
        ("Apriori متعدد المستويات", 0.555, "#9CC1E8"),
        ("الأكثر مبيعاً", 0.150, "#B3B3B3"),
        ("عشوائي", 0.009, "#D9D9D9"),
    ], 220)

    y = chart("التوصيات الشخصية (Item-CF) — هل توقعنا مشتريات الزبون القادمة؟", [
        ("Item-based CF", 0.223, "#82B366"),
        ("الأكثر مبيعاً", 0.125, "#B3B3B3"),
        ("ملف محتوى فقط", 0.071, "#A8D08D"),
        ("عشوائي", 0.011, "#D9D9D9"),
    ], y + 15)

    p.box("<b>الخلاصة:</b> في إكمال السلة Hybrid أدق بنحو 4× من «الأكثر مبيعاً» (59% مقابل 15%). "
          "وفي التخصيص Item-CF ≈ ضعف الشعبية (22% مقابل 13%). MRR للتهجين ≈ 0.50 أي أن الإجابة الصحيحة غالباً في المراتب الأولى.",
          80, y + 10, 1440, 70, ST["note"])
    return p


def main():
    pages = [
        dataset(), overview(), system_flow(), cross_sell(),
        upsell(), personalization(), journey(), evaluation(),
    ]
    xml = '<mxfile host="app.diagrams.net" type="device">' + "".join(
        pg.xml(i + 1) for i, pg in enumerate(pages)
    ) + "</mxfile>"
    out = Path(__file__).with_name("noor-store-algorithms.drawio")
    out.write_text(xml, encoding="utf-8")
    print(f"Wrote {out.name} ({len(pages)} pages)")


if __name__ == "__main__":
    main()
