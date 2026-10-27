<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Обращение с сайта</title>
</head>
<body style="font-family: Arial, sans-serif; color: #172033;">
<h1>Новое обращение с сайта</h1>

<table
    cellpadding="10"
    cellspacing="0"
    border="1"
    style="width: 100%; border-collapse: collapse; border-color: #ddd;"
>
    <tbody>
    <tr>
        <th align="left">Тема</th>
        <td>{{ $data['subject'] }}</td>
    </tr>
    <tr>
        <th align="left">ФИО</th>
        <td>{{ $data['name'] }}</td>
    </tr>
    <tr>
        <th align="left">Телефон</th>
        <td>{{ $data['phone'] }}</td>
    </tr>
    <tr>
        <th align="left">E-mail</th>
        <td>{{ $data['email'] }}</td>
    </tr>
    <tr>
        <th align="left">Сообщение</th>
        <td style="white-space: pre-wrap;">{{ $data['message'] }}</td>
    </tr>
    </tbody>
</table>
</body>
</html>
