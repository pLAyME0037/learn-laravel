<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        http-equiv="Content-Type"
        content="text/html;">
    <meta width=device-width,
        initial-scale="1.0">
    <title>Transcript - {{ $student->student_id }}</title>
    <style>
        @font-face {
            font-family: 'KhmerFont';
            src: url('{{ $fontSrc }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'KhmerFont', 'sans-serif';
            color: #333;
        }

        .header {
            border-bottom: 2px solid #444;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .meta {
            width: 100%;
            margin-bottom: 20px;
        }

        .meta td {
            padding: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;
        }

        .semester-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .gpa-box {
            text-align: right;
            margin-top: 30px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="logo">University</div>
        <div style="font-size: 12px; color: #666;">Official Academic Transcript</div>
    </div>

    <table class="meta"
        style="border: none;">
        <tr style="border: none;">
            <td style="border: none; width: 50%;">
                <strong class="font-khmer">Name:</strong> {{ $student->user->name }}<br>
                <strong>Student ID:</strong> {{ $student->student_id }}
            </td>
            <td style="border: none; width: 50%; text-align: right;">
                <strong>Program:</strong> {{ $student->program->name }}<br>
                <strong>Date:</strong> {{ $generatedAt }}
            </td>
        </tr>
    </table>

    @foreach ($transcriptData as $semesterName => $records)
        <div class="semester-title">{{ $semesterName }}</div>
        <table>
            <thead>
                <tr>
                    <th width="15%">Code</th>
                    <th width="50%">Course Title</th>
                    <th width="10%">Credits</th>
                    <th width="10%">Grade</th>
                    <th width="15%">Points</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $row)
                    <tr>
                        <td>{{ $row->classSession->course->code }}</td>
                        <td>{{ $row->classSession->course->name }}</td>
                        <td>{{ $row->classSession->course->credits }}</td>
                        <td>{{ $row->grade_letter }}</td>
                        <td>{{ $row->grade_points }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="gpa-box">
        <strong>Cumulative GPA:</strong> {{ $student->cgpa }}<br>
        <strong>Total Credits Earned:</strong> {{ $student->total_credits_earned }}
    </div>

</body>

</html>
