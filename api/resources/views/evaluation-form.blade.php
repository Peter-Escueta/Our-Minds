<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Assessment Evaluation Report</title>
    <style>
        body {
            font-family: Calibri, sans-serif;
            font-size: 13px;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 15px;
        }

        td,
        th {
            border: 1px solid black;
            padding: 6px;
            vertical-align: top;
        }

        .section-label {
            background-color: #888;
            color: white;
            font-weight: bold;
            padding: 8px;
            margin: 20px 0 10px 0;
        }

        .category-label {
            background-color: #e0e0e0;
            font-weight: bold;
            padding: 8px;
            margin: 15px 0 5px 0;
            border-left: 4px solid #888;
        }

        .age-label {
            background-color: #f5f5f5;
            font-weight: bold;
            padding: 6px;
            margin: 10px 0 5px 0;
            border-left: 3px solid #666;
        }

        .no-border {
            border: none !important;
        }

        h2 {
            text-align: center;
            margin: 30px 0 10px;
            text-transform: uppercase;
        }

        h3 {
            margin: 15px 0 5px;
        }

        p {
            text-align: justify;
            margin: 8px 0;
        }

        ul {
            margin: 8px 0;
            padding-left: 25px;
        }

        li {
            margin-bottom: 5px;
        }

        .competency {
            font-style: italic;
            margin: 10px 0;
            padding-left: 8px;
        }

        .footer {
            margin-top: 30px;
        }

        .disclaimer {
            font-style: italic;
            margin: 15px 0;
        }

        .signature {
            margin-top: 50px;
        }

        .age-badge {
            display: inline-block;
            background: #666;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/pdf-header.jpg') }}" alt="Logo"
            style="width: 100%; max-width: 100%; height: auto;">
    </div>

    <table>
        <tr>
            <td>Surname
                <br>
                {{ $child->surname }}
            </td>
            <td>First Name
                <br>
                {{ $child->first_name }}
            </td>
            <td>Middle Name
                <br>
                {{ $child->middle_name }}
            </td>
            <td rowspan="1">
                O Initial O Follow Up
                <br>
                <strong>Current Educational Placement</strong>
            </td>
        </tr>
        <tr>
            <td colspan="3"><strong>Address:</strong> {{ $child->address }}</td>
            <td><strong>Email:</strong> {{ $child->email }}</td>
        </tr>

        <tr>
            <td><strong>Date of Birth</strong><br>{{ $child->date_of_birth->format('m/d/Y') }}</td>
            <td><strong>Date of Assessment</strong><br>{{ $child->date_of_assessment->format('m/d/Y') }}</td>
            <td><strong>Age at Consult</strong><br>{{ $child->age_at_consult }}</td>
            <td><strong>Gender</strong>: {{ $child->gender }}<br><strong>Siblings</strong>: {{ $child->siblings }}</td>
        </tr>

        <tr>
            <td colspan="2">
                <strong>Mother's Name/Occupation/Contact Number</strong><br>
                {{ $child->mother_name }} / {{ $child->mother_occupation }} / {{ $child->mother_contact }}
            </td>
            <td colspan="2">
                <strong>Father's Name/Occupation/Contact Number</strong><br>
                {{ $child->father_name }} / {{ $child->father_occupation }} / {{ $child->father_contact }}
            </td>
        </tr>

        <tr>
            <td><strong>Medical Diagnosis/Impression</strong><br>{{ $child->medical_diagnosis }}</td>
            <td><strong>Referring Doctor</strong><br>{{ $child->referring_doctor }}</td>
            <td><strong>Date of Assessment</strong><br>{{ $child->date_of_assessment->format('m/d/Y') }}</td>
            <td>
                <strong>Last Assessment:</strong> {{ $child->last_assessment_date?->format('m/d/Y') ?? 'N/A' }}<br>
                <strong>Follow-Up Date:</strong> {{ $child->follow_up_date?->format('m/d/Y') ?? 'N/A' }}
            </td>
        </tr>

        <tr>
            <td><strong>Occupational Therapy</strong><br>{{ $child->therapies->firstWhere('type', 'occupational_therapy') ? 'Yes' : 'No' }}</td>
            <td><strong>Physical Therapy</strong><br>{{ $child->therapies->firstWhere('type', 'physical_therapy') ? 'Yes' : 'No' }}</td>
            <td><strong>Behavioral Therapy</strong><br>{{ $child->therapies->firstWhere('type', 'behavioral_therapy') ? 'Yes' : 'No' }}</td>
            <td><strong>Speech Therapy</strong><br>{{ $child->therapies->firstWhere('type', 'speech_therapy') ? 'Yes' : 'No' }}</td>
        </tr>

        <tr>
            <td><strong>School</strong><br>{{ $child->school }}</td>
            <td><strong>Grade</strong><br>{{ $child->grade }}</td>
            <td><strong>Placement</strong><br>{{ $child->placement }}</td>
            <td><strong>Year</strong><br>{{ $child->year }}</td>
        </tr>

        <tr>
            <td colspan="4" class="section-label">* REASON FOR CONSULTATION</td>
        </tr>
        <tr>
            <td colspan="4">{{ $child->reason ?? '[Reason for consultation goes here]' }}</td>
        </tr>
    </table>

    <div class="section-label">BACKGROUND INFORMATION</div>
    <p>{{ $evaluation->background_information }}</p>

    <div class="section-label">DEVELOPMENTAL AREAS (sensory-motor, cognitive, language, psychosocial & self-help skills)
    </div>
    <p>The assessment result shows that {{ $child->first_name }}:</p>

    @php
        $groupedCategories = [];
        foreach ($categories as $category) {
            $groupedCategories[$category['name']][] = $category;
        }
    @endphp

    @foreach ($groupedCategories as $categoryName => $ageCategories)
        <div class="category-label">
            {{ strtoupper($categoryName) }}
            @if(count($ageCategories) > 1)
                @foreach($ageCategories as $ageCat)
                    <span class="age-badge">Age {{ $ageCat['age'] }}</span>
                @endforeach
            @endif
        </div>

        @foreach($ageCategories as $category)
            @if(count($ageCategories) > 1)
                <div class="age-label">Age {{ $category['age'] }} Assessment</div>
            @endif

            <ul>
                @foreach ($category['responses'] as $response)
                    <li>{{ $response }}</li>
                @endforeach
            </ul>
            <div class="competency">
                <em>{{ $category['competency'] }}</em>
            </div>

            @if(!$loop->last)
                <div style="margin: 15px 0; border-bottom: 1px dashed #ccc;"></div>
            @endif
        @endforeach
    @endforeach

    <i>
        <p style="text-indent: 4em;">{{ $evaluation->summary_notes }}</p>
    </i>

    <div class="section-label">RECOMMENDATIONS</div>
    <ul>
        @foreach ($evaluation->recommendations as $recommendation)
            <li>{{ $recommendation }}</li>
        @endforeach
    </ul>

    <div class="section-label">RECOMMENDED WEBSITES FOR PARENTS</div>
    <ul>
              @foreach ($evaluation->websites as $website)
            <li>{{ $website }}</li>
        @endforeach
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
            <p style="font-size:10px">Special Education Specialist/Consultant <br>
                LPT – 1172639 <br>
                RPm - 0002113
            </p>
        </div>
    </div>
</body>

</html>
