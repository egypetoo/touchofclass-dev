<?php

namespace Database\Seeders;

class CatalogSpecifications
{
    /**
     * Technical data read from the catalog PDFs in resources/design/data.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function definitions(): array
    {
        return [
            'KM-P-VANSAN' => self::product(
                'VAN SAN',
                'VSP SS 06010',
                ['en' => 'Stainless steel submersible pumps, 50 Hz. The 6 inch VSP SS 06010 has an outside diameter of 132 mm, 6 vanes and a 2 1/2 inch internal outlet.', 'ar' => 'طلمبات غاطسة استانلس ستيل 50 هرتز. موديل 6 بوصة VSP SS 06010 قطره الخارجي 132 مم، وعدد الريش 6، ومخرج داخلي 2.5 بوصة.'],
                [
                    self::fact('fa-ruler-horizontal', 'Diameter', 'القطر', '6"'),
                    self::fact('fa-circle-notch', 'Outside diameter', 'القطر الخارجي', '132 mm'),
                    self::fact('fa-arrows-left-right', 'Outlet', 'المخرج', '2 1/2"'),
                    self::fact('fa-gauge-high', 'Frequency', 'التردد', '50 Hz'),
                ],
                [
                    self::row('Brand', 'الماركة', 'VAN SAN'),
                    self::row('Type', 'النوع', ['en' => 'Stainless steel submersible pump', 'ar' => 'طلمبة غاطسة استانلس ستيل']),
                    self::row('Series', 'السلسلة', 'VSP SS'),
                    self::row('Model', 'الموديل', 'VSP SS 06010'),
                    self::row('Pump diameter', 'قطر المضخة', '6"'),
                    self::row('Outside diameter', 'القطر الخارجي', '132 mm'),
                    self::row('Vanes', 'عدد الريش', '6'),
                    self::row('Outlet', 'مقاس المخرج', '2 1/2"'),
                    self::row('Frequency', 'التردد', '50 Hz'),
                ],
            ),
            'KM-P-GOLD' => self::product(
                'GOLD',
                'GP',
                ['en' => 'Stainless steel submersible pumps in AISI 304. The catalogue lists delivery up to 270 m³/h, maximum sand content of 50 g/m³, and motor powers from 0.75 HP to 110 HP.', 'ar' => 'طلمبات غاطسة من استانلس ستيل AISI 304. الكتالوج يذكر تصرفًا حتى 270 م³/س، وحد أقصى للرمال 50 جم/م³، وقدرات من 0.75 إلى 110 حصان.'],
                [
                    self::fact('fa-water', 'Flow', 'التصرف', '270 m³/h'),
                    self::fact('fa-bolt', 'Power', 'القدرة', '0.75–110 HP'),
                    self::fact('fa-gem', 'Material', 'الخامة', 'AISI 304'),
                    self::fact('fa-hill-rockslide', 'Max sand', 'أقصى رمال', '50 g/m³'),
                ],
                [
                    self::row('Brand', 'الماركة', 'GOLD'),
                    self::row('Type', 'النوع', ['en' => 'Submersible pump', 'ar' => 'طلمبة غاطسة']),
                    self::row('Material', 'الخامة', 'AISI 304'),
                    self::row('Maximum flow', 'أقصى تصرف', '270 m³/h'),
                    self::row('Maximum sand', 'أقصى محتوى رمال', '50 g/m³'),
                    self::row('Motor power', 'قدرة الموتور', '0.75–110 HP'),
                    self::row('Example diameter', 'مثال للقطر', '8"'),
                ],
                ['0.75 HP', '1 HP', '1.5 HP', '2 HP', '3 HP', '40 HP', '50 HP', '60 HP', '70 HP', '100 HP', '110 HP'],
            ),
            'KM-P-SP' => self::product(
                'SP',
                '6SP17 / 6SP30',
                ['en' => '6 inch submersible water pumps. Performance data is published at 50 Hz and 2850 r/min, for 220–240 V single phase and 380–415 V three phase, with motor powers from 3 HP to 40 HP.', 'ar' => 'طلمبات مياة غاطسة 6 بوصة. بيانات الأداء على 50 هرتز و2850 لفة/د، لجهد 220–240 فولت فاز واحد و380–415 فولت ثلاثة فاز، بقدرات من 3 إلى 40 حصان.'],
                [
                    self::fact('fa-water', 'Flow', 'التصرف', '42 m³/h'),
                    self::fact('fa-bolt', 'Power', 'القدرة', '3–40 HP'),
                    self::fact('fa-plug', 'Voltage', 'الجهد', '220–415 V'),
                    self::fact('fa-ruler-horizontal', 'Diameter', 'القطر', '6"'),
                ],
                [
                    self::row('Brand', 'الماركة', 'SP'),
                    self::row('Type', 'النوع', ['en' => 'Submersible water pump', 'ar' => 'طلمبة مياة غاطسة']),
                    self::row('Models', 'الموديلات', '6SP17 / 6SP30'),
                    self::row('Diameter', 'قطر المضخة', '6"'),
                    self::row('Frequency', 'التردد', '50 Hz'),
                    self::row('Speed', 'السرعة', '2850 r/min'),
                    self::row('Voltage', 'الجهد', '220–240 V / 380–415 V'),
                    self::row('Motor power', 'قدرة الموتور', '3–40 HP (2.2–30 kW)'),
                    self::row('Delivery', 'التصرف', '24 m³/h (6SP17) / 42 m³/h (6SP30)'),
                ],
                ['3 HP', '4 HP', '5.5 HP', '7.5 HP', '10 HP', '12.5 HP', '15 HP', '17.5 HP', '20 HP', '25 HP', '30 HP', '40 HP'],
            ),
            'KM-P-TESLA' => self::product(
                'TESLA',
                '6TSP17',
                ['en' => '6 inch TSP17 submersible pump. The performance table is for 50 Hz and 380 V three phase, with motor powers from 1 HP / 0.75 kW.', 'ar' => 'طلمبة غاطسة 6 بوصة موديل TSP17. جدول الأداء على 50 هرتز و380 فولت ثلاثة فاز، بقدرات تبدأ من 1 حصان / 0.75 كيلووات.'],
                [
                    self::fact('fa-ruler-horizontal', 'Diameter', 'القطر', '6"'),
                    self::fact('fa-bolt', 'Power', 'القدرة', '1–20 HP'),
                    self::fact('fa-plug', 'Voltage', 'الجهد', '380 V'),
                    self::fact('fa-gauge-high', 'Frequency', 'التردد', '50 Hz'),
                ],
                [
                    self::row('Brand', 'الماركة', 'TESLA'),
                    self::row('Type', 'النوع', ['en' => 'Submersible pump', 'ar' => 'طلمبة غاطسة']),
                    self::row('Model', 'الموديل', '6TSP17'),
                    self::row('Diameter', 'قطر المضخة', '6"'),
                    self::row('Frequency', 'التردد', '50 Hz'),
                    self::row('Voltage', 'الجهد', '380 V'),
                    self::row('Phases', 'الفاز', ['en' => 'Three phase', 'ar' => 'ثلاثة فاز']),
                    self::row('Motor power', 'قدرة الموتور', '1–20 HP'),
                ],
                ['1 HP', '1.5 HP', '3 HP', '4 HP', '5.5 HP', '12.5 HP', '15 HP', '17.5 HP', '20 HP'],
            ),
            'KM-P-KPS' => self::product(
                'KPS',
                '4" NORLY',
                ['en' => '4 inch K.P.S submersible pumps. The performance sheet lists an outside diameter of 98 mm, with flow, stages and outlet shown per pump type.', 'ar' => 'طلمبات K.P.S غاطسة 4 بوصة. ورقة الأداء تذكر قطرًا خارجيًا 98 مم، مع التصرف والمراحل والمخرج لكل نوع.'],
                [
                    self::fact('fa-ruler-horizontal', 'Diameter', 'القطر', '4"'),
                    self::fact('fa-circle-notch', 'Outside diameter', 'القطر الخارجي', '98 mm'),
                ],
                [
                    self::row('Brand', 'الماركة', 'K.P.S'),
                    self::row('Type', 'النوع', ['en' => 'Submersible pump', 'ar' => 'طلمبة غاطسة']),
                    self::row('Series', 'السلسلة', '4" NORLY'),
                    self::row('Outside diameter', 'القطر الخارجي', '98 mm'),
                ],
            ),
            'KM-M-SP' => self::product(
                'STERA',
                '2"–6"',
                ['en' => 'Stera submersible motors from 2 inch to 6 inch. The catalogue covers cast-copper, cast-iron and stainless steel constructions.', 'ar' => 'مواتير Stera غاطسة من 2 إلى 6 بوصة. الكتالوج يشمل خامات النحاس المصبوب والحديد الزهر والاستانلس ستيل.'],
                [
                    self::fact('fa-ruler-horizontal', 'Diameter', 'القطر', '2"–6"'),
                    self::fact('fa-gem', 'Material', 'الخامة', 'SS / Cast iron'),
                ],
                [
                    self::row('Brand', 'الماركة', 'STERA'),
                    self::row('Type', 'النوع', ['en' => 'Submersible motor', 'ar' => 'موتور غاطس']),
                    self::row('Diameters', 'الأقطار', '2" / 3" / 4" / 5" / 6"'),
                    self::row('Materials', 'الخامات', ['en' => 'Cast copper, cast iron, stainless steel', 'ar' => 'نحاس مصبوب، حديد زهر، استانلس ستيل']),
                ],
            ),
            'KM-M-POLDAP' => self::product(
                'POLDAP',
                '4" OM / WM',
                ['en' => '4 inch oil-cooled submersible motors. Single phase 230 V from 0.5 HP and three phase 380 V up to 10 HP, at about 2810–2850 r/min. Protection IP68 and insulation class F.', 'ar' => 'مواتير غاطسة 4 بوصة مبردة بالزيت. فاز واحد 230 فولت من 0.5 حصان، وثلاثة فاز 380 فولت حتى 10 حصان، بسرعة تقارب 2810–2850 لفة/د. الحماية IP68 والعزل فئة F.'],
                [
                    self::fact('fa-bolt', 'Power', 'القدرة', '0.5–10 HP'),
                    self::fact('fa-plug', 'Voltage', 'الجهد', '230 / 380 V'),
                    self::fact('fa-ruler-horizontal', 'Diameter', 'القطر', '4"'),
                    self::fact('fa-gauge-high', 'Speed', 'السرعة', '2850 RPM'),
                ],
                [
                    self::row('Brand', 'الماركة', 'POLDAP'),
                    self::row('Type', 'النوع', ['en' => 'Oil-cooled submersible motor', 'ar' => 'موتور غاطس مبرد بالزيت']),
                    self::row('Series', 'السلسلة', '4" OM / WM'),
                    self::row('Power', 'القدرة', '0.5–10 HP'),
                    self::row('Voltage', 'الجهد', '230 V / 380 V'),
                    self::row('Speed', 'السرعة', '2810–2850 RPM'),
                    self::row('Flange', 'الفلانشة', '4" NEMA'),
                    self::row('Protection', 'درجة الحماية', 'IP68'),
                    self::row('Insulation', 'درجة العزل', 'F'),
                    self::row('Casing', 'الجسم', 'AISI 304'),
                    self::row('Liquid temperature', 'حرارة السائل', '0–35 °C'),
                ],
                ['0.5 HP', '0.75 HP', '1 HP', '1.5 HP', '2 HP', '3 HP', '4 HP', '5.5 HP', '7.5 HP', '10 HP'],
            ),
            'KM-M-KPS' => self::product(
                'K.P.S',
                '6" / 8"',
                ['en' => '6 and 8 inch submersible motors. The 6 inch 50 Hz table runs from 5.5 HP to 50 HP at 220, 380 and 400 V. Stator casing is AISI 304 stainless steel with a silicon carbide mechanical seal.', 'ar' => 'مواتير غاطسة 6 و8 بوصة. جدول 6 بوصة على 50 هرتز من 5.5 إلى 50 حصان عند 220 و380 و400 فولت. جسم الستاتور استانلس AISI 304 ومانع التسرب كربيد السيليكون.'],
                [
                    self::fact('fa-bolt', 'Power', 'القدرة', '5.5–50 HP'),
                    self::fact('fa-plug', 'Voltage', 'الجهد', '220–400 V'),
                    self::fact('fa-ruler-horizontal', 'Diameter', 'القطر', '6" / 8"'),
                    self::fact('fa-gauge-high', 'Frequency', 'التردد', '50 Hz'),
                ],
                [
                    self::row('Brand', 'الماركة', 'K.P.S'),
                    self::row('Type', 'النوع', ['en' => 'Submersible motor', 'ar' => 'موتور غاطس']),
                    self::row('Diameters', 'الأقطار', '6" / 8"'),
                    self::row('Power', 'القدرة', '5.5–50 HP (6")'),
                    self::row('Voltage', 'الجهد', '220 / 380 / 400 V'),
                    self::row('Frequency', 'التردد', '50 Hz'),
                    self::row('Speed', 'السرعة', '2860–2895 RPM'),
                    self::row('Stator casing', 'جسم الستاتور', 'AISI 304'),
                    self::row('Shaft', 'العمود', 'AISI 431 (6") / AISI 420 (8")'),
                    self::row('Mechanical seal', 'مانع التسرب', ['en' => 'Silicon carbide', 'ar' => 'كربيد السيليكون']),
                ],
                ['5.5 HP', '7.5 HP', '10 HP', '12.5 HP', '15 HP', '17.5 HP', '20 HP', '25 HP', '30 HP', '35 HP', '40 HP', '50 HP'],
            ),
            'KM-M-VANSAN' => self::product(
                'VAN SAN',
                '3S',
                ['en' => '3S submersible motors built entirely from stainless steel and wound with PE2+PA insulated wire.', 'ar' => 'مواتير 3S غاطسة مصنوعة بالكامل من الاستانلس ستيل، وملفوفة بسلك معزول PE2+PA.'],
                [
                    self::fact('fa-gem', 'Material', 'الخامة', 'Stainless steel'),
                    self::fact('fa-bolt', 'Winding', 'الملف', 'PE2+PA'),
                ],
                [
                    self::row('Brand', 'الماركة', 'VAN SAN'),
                    self::row('Type', 'النوع', ['en' => 'Submersible motor', 'ar' => 'موتور غاطس']),
                    self::row('Series', 'السلسلة', '3S'),
                    self::row('Construction', 'التصنيع', ['en' => 'Full stainless steel', 'ar' => 'استانلس ستيل بالكامل']),
                    self::row('Winding', 'الملف', 'PE2+PA'),
                ],
            ),
            'KM-M-IPMT' => self::product(
                'IPMT',
                null,
                ['en' => 'IPMT, Industrial Pump Motor Technology. The attached catalogue is the IPMT pump and motor data file.', 'ar' => 'IPMT لتقنية مواتير ومضخات الصناعة. الملف المرفق هو كتالوج بيانات IPMT.'],
                [],
                [
                    self::row('Brand', 'الماركة', 'IPMT'),
                    self::row('Type', 'النوع', ['en' => 'Industrial pump motor', 'ar' => 'موتور طلمبات صناعي']),
                ],
            ),
            'KM-PIPE-BORE' => self::product(
                'Bore Power',
                null,
                ['en' => 'uPVC column pipes for borehole submersible pumps, using Wirelock joints. The manual rates them for boreholes up to 1000 feet.', 'ar' => 'مواسير أعمدة uPVC لطلمبات الآبار الغاطسة بوصلة Wirelock. الدليل يصنفها لآبار حتى 1000 قدم.'],
                [
                    self::fact('fa-gem', 'Material', 'الخامة', 'uPVC'),
                    self::fact('fa-arrow-up-wide-short', 'Depth', 'العمق', '1000 ft'),
                ],
                [
                    self::row('Brand', 'الماركة', 'Bore Power'),
                    self::row('Type', 'النوع', ['en' => 'uPVC column pipe', 'ar' => 'ماسورة عمود uPVC']),
                    self::row('Joint', 'الوصلة', 'Wirelock'),
                    self::row('Rated depth', 'العمق', '1000 ft'),
                ],
            ),
            'KM-PIPE-PURELIFE' => self::product(
                'Pure Life',
                '1"–6"',
                ['en' => 'uPVC submersible column pipes from 1 inch to 6 inch. The technical chart lists safe hydrostatic pressure up to 35 kg/cm² and pump delivery head up to 350 m.', 'ar' => 'مواسير أعمدة uPVC غاطسة من 1 إلى 6 بوصة. الجدول الفني يذكر ضغطًا هيدروستاتيكيًا آمنًا حتى 35 كجم/سم² وضاغط طلمبة حتى 350 م.'],
                [
                    self::fact('fa-ruler-horizontal', 'Diameter', 'القطر', '1"–6"'),
                    self::fact('fa-arrow-up-wide-short', 'Head', 'الضغط', '350 m'),
                    self::fact('fa-gauge-high', 'Pressure', 'التحمل', '35 kg/cm²'),
                    self::fact('fa-gem', 'Material', 'الخامة', 'uPVC'),
                ],
                [
                    self::row('Brand', 'الماركة', 'Pure Life'),
                    self::row('Type', 'النوع', ['en' => 'Submersible column pipe', 'ar' => 'ماسورة عمود غاطسة']),
                    self::row('Material', 'الخامة', 'uPVC'),
                    self::row('Nominal diameter', 'القطر الاسمي', '1"–6"'),
                    self::row('Safe hydrostatic pressure', 'الضغط الهيدروستاتيكي الآمن', '35 kg/cm²'),
                    self::row('Pump delivery head', 'ضاغط الطلمبة', '350 m'),
                ],
                ['1"', '1 1/4"', '1 1/2"', '2"', '2 1/2"', '3"', '4"', '5"', '6"'],
                ['en' => 'Diameter', 'ar' => 'القطر'],
            ),
            'KM-PIPE-ASHIRVAD' => self::product(
                'Ashirvad',
                null,
                ['en' => 'uPVC column pipes for submersible pumps, with the Wirelock joint. The manual rates installation down to 1200 feet, and the pressure chart includes hydrostatic ratings up to 40 kg/cm².', 'ar' => 'مواسير أعمدة uPVC للطلمبات الغاطسة بوصلة Wirelock. الدليل يصنف التركيب حتى 1200 قدم، وجدول الضغط يصل إلى 40 كجم/سم².'],
                [
                    self::fact('fa-gem', 'Material', 'الخامة', 'uPVC'),
                    self::fact('fa-arrow-up-wide-short', 'Depth', 'العمق', '1200 ft'),
                    self::fact('fa-gauge-high', 'Pressure', 'التحمل', '40 kg/cm²'),
                ],
                [
                    self::row('Brand', 'الماركة', 'Ashirvad'),
                    self::row('Type', 'النوع', ['en' => 'uPVC column pipe', 'ar' => 'ماسورة عمود uPVC']),
                    self::row('Joint', 'الوصلة', 'Wirelock'),
                    self::row('Rated depth', 'العمق', '1200 ft'),
                    self::row('Hydrostatic pressure', 'الضغط الهيدروستاتيكي', '40 kg/cm²'),
                ],
            ),
            'KM-PIPE-PURE' => self::product(
                'Pure Pipe',
                null,
                ['en' => 'uPVC column pipes and accessories for submersible and jet pumps. The catalogue describes them as a replacement for MS, GI, HDPE and stainless column pipes, with square threads and sealing rings.', 'ar' => 'مواسير أعمدة uPVC وملحقاتها للطلمبات الغاطسة والنفاثة. الكتالوج يصفها كبديل لمواسير MS وGI وHDPE والاستانلس، بقلاووظ مربع وحلقات إحكام.'],
                [
                    self::fact('fa-gem', 'Material', 'الخامة', 'uPVC'),
                ],
                [
                    self::row('Brand', 'الماركة', 'Pure Pipe'),
                    self::row('Type', 'النوع', ['en' => 'uPVC column pipe', 'ar' => 'ماسورة عمود uPVC']),
                    self::row('Application', 'الاستخدام', ['en' => 'Submersible and jet pumps', 'ar' => 'طلمبات غاطسة ونفاثة']),
                    self::row('Joint', 'الوصلة', ['en' => 'Square thread with sealing ring', 'ar' => 'قلاووظ مربع بحلقة إحكام']),
                ],
            ),
            'KM-C-CARKIT' => self::product(
                'Carkit',
                'H07VVH6-F',
                ['en' => 'Flat PVC submersible pump cable, code H07VVH6-F, rated 450/750 V. Fine-stranded copper conductors, PVC insulation and sheath, for 3 and 4 core sections from 1.5 mm² to 240 mm².', 'ar' => 'كابل مسطح PVC للطلمبات الغاطسة، كود H07VVH6-F، بجهد 450/750 فولت. موصل نحاس مرن وعزل وغلاف PVC، بمقاطع 3 و4 قلب من 1.5 إلى 240 مم².'],
                [
                    self::fact('fa-plug', 'Voltage', 'الجهد', '450/750 V'),
                    self::fact('fa-temperature-half', 'Temperature', 'الحرارة', '70 °C'),
                    self::fact('fa-circle-notch', 'Section', 'المقطع', '1.5–240 mm²'),
                ],
                [
                    self::row('Brand', 'الماركة', 'Carkit'),
                    self::row('Type', 'النوع', ['en' => 'Flat submersible pump cable', 'ar' => 'كابل مسطح لطلمبات غاطسة']),
                    self::row('Code', 'الكود', 'H07VVH6-F'),
                    self::row('Standard', 'المواصفة', 'IEC 60227-6'),
                    self::row('Rated voltage', 'الجهد المقنن', '450/750 V'),
                    self::row('Operating temperature', 'حرارة التشغيل', '70 °C'),
                    self::row('Short-circuit temperature', 'حرارة القصر', '160 °C'),
                    self::row('Conductor', 'الموصل', ['en' => 'Fine-stranded copper', 'ar' => 'نحاس مرن متعدد الشعيرات']),
                    self::row('Sections', 'المقاطع', '3×1.5 – 4×240 mm²'),
                ],
            ),
            'KM-C-AXELON' => self::product(
                'Axelon',
                null,
                ['en' => 'Axelon cable catalogue. The submersible range in the index covers PVC 3 and 4 core flat cable and submersible winding wire.', 'ar' => 'كتالوج كابلات Axelon. نطاق الأعماق في الفهرس يشمل كابل PVC مسطح 3 و4 قلب وسلك لف غاطس.'],
                [],
                [
                    self::row('Brand', 'الماركة', 'Axelon'),
                    self::row('Type', 'النوع', ['en' => 'Submersible cable', 'ar' => 'كابل غاطس']),
                    self::row('Constructions', 'التكوين', ['en' => 'PVC 3 and 4 core flat cable, submersible winding wire', 'ar' => 'كابل PVC مسطح 3 و4 قلب، وسلك لف غاطس']),
                ],
            ),
            'KM-C-SEVAL' => self::product(
                'SEVAL',
                'H07VVH6-F',
                ['en' => 'PVC sheathed flat cable for lifts and submersible pumps, code H07VVH6-F / IEC 60227-6, rated 450/750 V, with fine-stranded class 5 copper.', 'ar' => 'كابل مسطح بغلاف PVC للمصاعد والطلمبات الغاطسة، كود H07VVH6-F، بجهد 450/750 فولت وموصل نحاس فئة 5.'],
                [
                    self::fact('fa-plug', 'Voltage', 'الجهد', '450/750 V'),
                    self::fact('fa-circle-notch', 'Section', 'المقطع', '3×1.5 mm²'),
                ],
                [
                    self::row('Brand', 'الماركة', 'SEVAL'),
                    self::row('Type', 'النوع', ['en' => 'Flat submersible pump cable', 'ar' => 'كابل مسطح لطلمبات غاطسة']),
                    self::row('Code', 'الكود', 'H07VVH6-F / 60227 IEC 71f'),
                    self::row('Standard', 'المواصفة', 'IEC 60227-6'),
                    self::row('Rated voltage', 'الجهد المقنن', '450/750 V'),
                    self::row('Conductor', 'الموصل', ['en' => 'Fine-stranded copper, class 5', 'ar' => 'نحاس مرن فئة 5']),
                    self::row('Sheath', 'الغلاف', 'PVC'),
                ],
            ),
            'KM-INV-DELIXI' => self::product(
                'DELIXI',
                'CDI-SPD (C1)',
                ['en' => 'Solar pump inverter that converts DC from the solar array to AC for the pump. MPPT efficiency is up to 99.9%, AC input is 380 V three phase, maximum DC voltage is 900 V, and power goes up to 110 kW.', 'ar' => 'انفرتر طلمبات شمسية يحوّل تيار الألواح المستمر إلى متردد لتشغيل الطلمبة. كفاءة MPPT حتى 99.9٪، ودخل متردد 380 فولت ثلاثة فاز، وأقصى جهد مستمر 900 فولت، والقدرة حتى 110 كيلووات.'],
                [
                    self::fact('fa-bolt', 'Power', 'القدرة', '110 kW'),
                    self::fact('fa-plug', 'AC voltage', 'جهد المتردد', '380 V'),
                    self::fact('fa-solar-panel', 'Max DC', 'أقصى مستمر', '900 V'),
                    self::fact('fa-gauge-high', 'MPPT', 'MPPT', '99.9%'),
                ],
                [
                    self::row('Brand', 'الماركة', 'DELIXI'),
                    self::row('Type', 'النوع', ['en' => 'Solar pump inverter', 'ar' => 'انفرتر طلمبة شمسية']),
                    self::row('Model', 'الموديل', 'CDI-SPD (C1)'),
                    self::row('AC input', 'دخل المتردد', '380 V ±15% (3PH)'),
                    self::row('Maximum DC voltage', 'أقصى جهد مستمر', '800 V / 900 V'),
                    self::row('Power', 'القدرة', '110 kW'),
                    self::row('MPPT efficiency', 'كفاءة MPPT', '99.9%'),
                ],
            ),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $facts
     * @param  list<array<string, mixed>>  $rows
     * @param  list<string>  $options
     * @param  array{en: string, ar: string}|null  $optionsLabel
     * @return array<string, mixed>
     */
    private static function product(string $brand, ?string $model, array $summary, array $facts, array $rows, array $options = [], ?array $optionsLabel = null): array
    {
        return [
            'brand' => $brand,
            'model' => $model,
            'summary' => $summary,
            'facts' => $facts,
            'rows' => $rows,
            'options' => $options,
            'options_label' => $optionsLabel ?? ['en' => 'Power', 'ar' => 'القدرة'],
        ];
    }

    /**
     * @return array{icon: string, label: array{en: string, ar: string}, value: string}
     */
    private static function fact(string $icon, string $en, string $ar, string $value): array
    {
        return [
            'icon' => $icon,
            'label' => ['en' => $en, 'ar' => $ar],
            'value' => $value,
        ];
    }

    /**
     * @return array{label: array{en: string, ar: string}, value: string|array{en: string, ar: string}}
     */
    private static function row(string $en, string $ar, string|array $value): array
    {
        return [
            'label' => ['en' => $en, 'ar' => $ar],
            'value' => $value,
        ];
    }
}
