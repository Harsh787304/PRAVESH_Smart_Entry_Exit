<!DOCTYPE html>
<html>
<head>
    <title>Reason for Leaving</title>
</head>
<body>
    <h1>Reason for Leaving</h1>
    <form action="submit_form.php" method="post">
        <input type="hidden" name="uid" value="<?php echo htmlspecialchars($_GET['uid']); ?>">
        <label for="reason">Reason for Leaving:</label>
        <textarea id="reason" name="reason" rows="4" cols="50"></textarea><br><br>
        <label for="return_time">Expected Return Time(in hr):</label>
        <input type="text" id="return_time" name="return_time"><br><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
