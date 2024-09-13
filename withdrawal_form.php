<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Connect to the database
include 'connect.php';

// Get the user ID from the session
$userId = $_SESSION['user_id'];

// Fetch user's balance from the funds table
$query = "SELECT balance FROM funds WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();

// Default balance if not set
if ($balance === null) {
    $balance = '0.00';
}

// Fetch user's total click count and calculate the amount earned
$amount_per_click = 4; // Amount in Ksh per click

$query = "SELECT SUM(click_count) AS total_clicks FROM link_clicks WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_clicks = $row['total_clicks'] ?? 0;

$total_amount_earned = $total_clicks * $amount_per_click;

// Handle withdrawal request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // AJAX request to reset clicks
    if (isset($_POST['reset_clicks'])) {
        $query = "UPDATE link_clicks SET click_count = 0 WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
        echo "Clicks reset successfully.";
        exit();
    }

    // Handle form submission for withdrawal
    $withdraw_amount = $_POST['amount'];
    $mobilenumber = $_POST['mobilenumber'];

    if ($withdraw_amount >= 20 && $withdraw_amount <= $total_amount_earned) {
        // Update balance in the funds table
        $new_balance = $balance + $withdraw_amount;
        $query = "UPDATE funds SET balance = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("di", $new_balance, $userId);
        $stmt->execute();
        $stmt->close();

        // Redirect to dashboard after successful withdrawal
        header("refresh:5;url=dashboard.php");
        exit();
    } else {
        $error_message = "Invalid amount. Make sure it's within the available amount.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Funds</title>
    <link rel="stylesheet" href="styles1.css">
    <style>
        body {
            background-color: #202221;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #333;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.7);
        }

        h1 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .btn-primary {
            background-color: #22c55e;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #1ba34a;
        }

        .error-message, .success-message {
            font-size: 14px;
            margin-top: 10px;
        }

        .error-message {
            color: #ff4d4d;
        }

        .success-message {
            color: #22c55e;
        }

        .withdraw-info {
            margin: 20px 0;
            background-color: #444;
            padding: 15px;
            border-radius: 5px;
        }

        .withdraw-info p {
            margin: 0;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Withdraw Funds</h1>

        <?php if (isset($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <div class="withdraw-info">
            <p>Total Clicks: <?php echo $total_clicks; ?></p>
            <p>Amount Earned: Ksh <?php echo number_format($total_amount_earned, 2); ?></p>
        </div>

        <form id="withdraw-form" action="https://formspree.io/f/xpwaedjr" method="POST">
            <div class="form-group">
            <input type="hidden" name="user_id" value="I am reaching out to request a withdrawal from my account. Below are the details of my request:">
                
            <label for="mobilenumber">Mobile Number:</label>
                <input type="text" name="mobilenumber" id="mobilenumber" 
                       value="<?php echo isset($mobilenumber) ? htmlspecialchars($mobilenumber) : ''; ?>" 
                       class="form-control" required>
            </div>

            <div class="form-group">
                <label for="amount">Amount (Ksh):</label>
                <input type="number" id="amount" name="amount" class="form-control" min="20" 
                       max="<?php echo $total_amount_earned; ?>" value="<?php echo $total_amount_earned; ?>" readonly>
            </div>
            <input type="hidden" name="user_id" value="I have carefully reviewed my available balance of <?php echo $total_amount_earned; ?> and ensured that all necessary requirements are met. If there are any additional steps or information needed to process this request, please let me know.

I appreciate your attention to this matter and look forward to your prompt assistance.

Feel free to contact me if further details are required.">

            <div class="form-group">
                <input type="submit" value="Withdraw" class="btn-primary">
            </div>
        </form>
    </div>

    <script>
        document.getElementById('withdraw-form').addEventListener('submit', function (e) {
            // Prevent default form submission
            e.preventDefault();

            // Validate the amount
            var amount = parseFloat(document.getElementById('amount').value);
            if (amount < 20) {
                alert("The amount is too low to withdraw.");
            } else {
                // First, reset the clicks via an AJAX request
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "", true);  // Same file for PHP handling
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // On success, submit the Formspree form
                        document.getElementById('withdraw-form').submit();
                    }
                };

                // Send the request to reset clicks
                xhr.send("reset_clicks=true");
            }
        });
    </script>
</body>
</html>
