<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Funds</title>
    <link rel="stylesheet" href="styles1.css"> <!-- Link to your CSS file -->
</head>
<body style="background-color: #202221;">
    <div class="container">
        <h1>Withdraw Funds</h1>
        <form action="withdraw.php" method="POST" class="withdrawal-form">
            <!-- Form group for mobile number -->
            <div class="form-group">
                <label for="mobilenumber">Mobile Number:</label>
                <input type="text" id="mobilenumber" name="mobilenumber" 
                       placeholder="Phone number to receive payment" 
                       class="form-control" required>
            </div>

            <!-- Form group for withdrawal amount -->
            <div class="form-group">
                <label for="amount">Amount (Ksh):</label>
                <input type="number" id="amount" name="amount" required min="1" 
                       value="<?php echo htmlspecialchars($balance); ?>" 
                       class="form-control">
            </div>

            <!-- Submit button -->
            <div class="form-group">
                <input type="submit" value="Withdraw" class="btn btn-primary">
            </div>
        </form>
    </div>
</body>
</html>
