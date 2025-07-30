<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Assessment Evaluation Report</title>
    <style>
        body {
            font-family: Calibri, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        h1 {
            text-align: center;
            font-size: 16pt;
            margin: 20px 0;
        }

        h2 {
            background-color: #333;
            color: white;
            padding: 5px 10px;
            font-size: 14pt;
            margin: 25px 0 10px;
        }

        h3 {
            font-size: 13pt;
            text-decoration: underline;
            margin: 15px 0 5px;
        }

        ul {
            margin: 5px 0;
            padding-left: 20px;
        }

        li {
            margin-bottom: 5px;
        }

        .competency {
            font-style: italic;
            margin: 10px 0 15px;
            padding-left: 20px;
        }

        .divider {
            border-top: 1px dashed #333;
            margin: 15px 0;
        }

        .footer {
            margin-top: 40px;
            font-size: 11pt;
        }

        .signature {
            margin-top: 60px;
        }

        .disclaimer {
            font-style: italic;
            margin-top: 30px;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ $logo ?? public_path('images/pdf-header.jpg') }}" alt="Logo"
            style="width: 100%; max-width: 100%; height: auto;">
    </div>

    <h1>Assessment Evaluation Report</h1>

    <!-- BACKGROUND INFORMATION -->
    <h2>BACKGROUND INFORMATION</h2>
    <p>{{ $evaluation->background_information }}</p>

    <!-- DEVELOPMENTAL AREAS -->
    <h2>DEVELOPMENTAL AREAS (sensory-motor, cognitive, language, psychosocial & self-help skills)</h2>
    <p>The assessment result shows that {{ $child->first_name }}:</p>

    @foreach ($categories as $category)
        <h3>{{ strtoupper($category['name']) }}</h3>
        <ul>
            @foreach ($category['responses'] as $response)
                <li>{{ $response }}</li>
            @endforeach
        </ul>
        <div class="competency">
            <em>{{ $category['competency'] }}</em>
        </div>
        @if (!$loop->last)
            <div class="divider"></div>
        @endif
    @endforeach

    <!-- FINAL SUMMARY -->
    <h2>FINAL SUMMARY</h2>
    <p>{{ $evaluation->summary_notes }}</p>

    <!-- RECOMMENDATIONS -->
    <h2>RECOMMENDATIONS</h2>
    <ul>
        @foreach ($evaluation->recommendations as $recommendation)
            <li>{{ $recommendation }}</li>
        @endforeach
    </ul>

    <!-- RECOMMENDED WEBSITES -->
    <h2>RECOMMENDED WEBSITES FOR PARENTS</h2>
    <ul>
        <li>Autism Speaks: www.autismspeaks.org</li>
        <li>Understood: www.understood.org</li>
        <li>Child Mind Institute: www.childmind.org</li>
    </ul>

    <div class="footer">
        <p>"It is very important to determine the appropriate educational placement and program of your child suited to
            his/her characteristics through a thorough, well-defined assessment, as it is the key to reaching his/her
            highest potential." - R. Mindanao</p>

        <div class="disclaimer">
            Disclaimer: This report is only for school placement and special education program design and intervention
            and shall not be used for diagnostic or any legal purposes.
        </div>

        <div class="signature">
            <p>Evaluated by:</p>
            <br><br>
            <p><strong>RAYMUND E. MINDANAO MA LPT RPm</strong></p>
            <p>Licensed Psychometrician</p>
        </div>
    </div>
</body>

</html>
