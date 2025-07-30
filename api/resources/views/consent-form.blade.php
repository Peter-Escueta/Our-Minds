<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Informed Consent</title>
    <style>
        body {
            font-family: Calibri, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
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
            text-align: left;
            padding: 8px;
        }

        .no-border {
            border: none !important;
        }

        h2 {
            text-align: center;
            margin: 30px 0 10px;
            text-transform: uppercase;
        }

        p {
            text-align: justify;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/pdf-header.jpg') }}" alt="Logo"
            style="width: 100%; max-width: 100%; height: auto;">
    </div>

    <table>
        <!-- Top row with name fields and placement -->
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

        <!-- DOB, Assessment, Age, Gender, Siblings -->
        <tr>
            <td><strong>Date of Birth</strong><br>{{ $child->date_of_birth->format('m/d/Y') }}</td>
            <td><strong>Date of Assessment</strong><br>{{ $child->date_of_assessment->format('m/d/Y') }}</td>
            <td><strong>Age at Consult</strong><br>{{ $child->age_at_consult }}</td>
            <td><strong>Gender</strong>: {{ $child->gender }}<br><strong>Siblings</strong>: {{ $child->siblings }}</td>
        </tr>

        <!-- Parents Info -->
        <tr>
            <td colspan="2">
                <strong>Mother’s Name/Occupation/Contact Number</strong><br>
                {{ $child->mother_name }} / {{ $child->mother_occupation }} / {{ $child->mother_contact }}
            </td>
            <td colspan="2">
                <strong>Father’s Name/Occupation/Contact Number</strong><br>
                {{ $child->father_name }} / {{ $child->father_occupation }} / {{ $child->father_contact }}
            </td>
        </tr>

        <!-- Medical Details -->
        <tr>
            <td><strong>Medical Diagnosis/Impression</strong><br>{{ $child->medical_diagnosis }}</td>
            <td><strong>Referring Doctor</strong><br>{{ $child->referring_doctor }}</td>
            <td><strong>Date of Assessment</strong><br>{{ $child->date_of_assessment->format('m/d/Y') }}</td>
            <td>
                <strong>Last Assessment:</strong> {{ $child->last_assessment_date?->format('m/d/Y') ?? 'N/A' }}<br>
                <strong>Follow-Up Date:</strong> {{ $child->follow_up_date?->format('m/d/Y') ?? 'N/A' }}
            </td>
        </tr>

        <!-- Therapy Section -->
        <tr>
            <td><strong>Occupational Therapy</strong><br>{{ $child->occupational_therapy ? 'Yes' : 'No' }}</td>
            <td><strong>Physical Therapy</strong><br>{{ $child->physical_therapy ? 'Yes' : 'No' }}</td>
            <td><strong>Behavioral Therapy</strong><br>{{ $child->behavioral_therapy ? 'Yes' : 'No' }}</td>
            <td><strong>Speech Therapy</strong><br>{{ $child->speech_therapy ? 'Yes' : 'No' }}</td>
        </tr>

        <!-- School Details -->
        <tr>
            <td><strong>School</strong><br>{{ $child->school }}</td>
            <td><strong>Grade</strong><br>{{ $child->grade }}</td>
            <td><strong>Placement</strong><br>{{ $child->placement }}</td>
            <td><strong>Year</strong><br>{{ $child->year }}</td>
        </tr>

        <!-- Reason for Consultation -->
        <tr>
            <td colspan="4" class="section-label">✱ REASON FOR CONSULTATION</td>
        </tr>
        <tr>
            <td colspan="4">{{ $child->reason_for_consultation ?? '[Reason for consultation goes here]' }}</td>
        </tr>
    </table>

    <h2>Informed Consent</h2>

    <p>
        I understand that I permit this institution to document my child (on any platform) following its nature and
        purpose.
        I also understand that the information I gave to this institution is true, and confidential and will not be
        released
        to any person or organization without my written permission (there is a release of Information consent form in
        our institution).
        The only exceptions to this policy are rare situations in which you are required by law to release information
        with or without
        my permission. These are: (1) if there is evidence of physical and/or sexual abuse of children or the elderly;
        (2) if you judge
        that I am in danger of harming myself or another individual; and (3) if my records are subpoenaed by the court.
        In the rare instance of any of these situations, you would limit disclosure of confidential information to the
        minimum necessary to ensure safety.
    </p>

    <p>
        I understand that assessments and therapies may present challenges for my child, potentially leading to
        temporary fatigue,
        frustration, discomfort, or resistance as they adapt to new demands. I also acknowledge that while
        confidentiality is a priority,
        relevant information may be shared among the support team to ensure coordinated care, and that individual
        progress can vary,
        with no guaranteed outcomes, which might highlight emotionally challenging areas for my child and family.
    </p>

    <p>
        By my signature below, I acknowledge my responsibility to provide accurate and complete information regarding my
        child's medical history,
        development, and current concerns, and to actively participate in all educational planning and discussions. I
        will ensure my child attends
        all scheduled assessment and therapy sessions, and I am fully aware of and will fulfill my financial obligations
        to this institution,
        including tuition, miscellaneous, and other fees.
    </p>

    <br><br>
    <p><strong>Parent/Guardian Signature:</strong> ____________________________</p>
    <p><strong>Date:</strong> ____________________________</p>
</body>

</html>
