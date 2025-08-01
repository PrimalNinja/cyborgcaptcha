<?php

// CyborgCaptcha v20250801
// (c) 2025 Cyborg Unicorn Pty Ltd.
// This software is released under MIT License.

session_start();

define("URL_SUCCESS", "https://cyborgshell.com");
define("URL_FAIL", "https://google.com");

class BinaryLogicCaptcha {
    
    private $arrChallenges = [
        [
            'question' => 'Binary AND: 11010110 & 10101011 = ?',
            'answer' => '10000010',
            'operation' => 'AND'
        ],
        [
            'question' => 'Binary OR: 01101001 | 10010110 = ?',
            'answer' => '11111111',
            'operation' => 'OR'
        ],
        [
            'question' => 'Binary XOR: 11001100 ^ 10101010 = ?',
            'answer' => '01100110',
            'operation' => 'XOR'
        ],
        [
            'question' => 'Binary NOT: ~10110011 = ? (8-bit)',
            'answer' => '01001100',
            'operation' => 'NOT'
        ],
        [
            'question' => 'Left shift: 00101101 << 2 = ?',
            'answer' => '10110100',
            'operation' => 'SHIFT_LEFT'
        ],
        [
            'question' => 'Right shift: 11010110 >> 3 = ?',
            'answer' => '00011010',
            'operation' => 'SHIFT_RIGHT'
        ],
        [
            'question' => 'NAND: ~(11110000 & 10101010) = ?',
            'answer' => '01011111',
            'operation' => 'NAND'
        ],
        [
            'question' => 'Convert binary 11010111 to hexadecimal:',
            'answer' => 'd7',
            'operation' => 'BIN_TO_HEX'
        ],
        [
            'question' => 'Two\'s complement of 01101010:',
            'answer' => '10010110',
            'operation' => 'TWOS_COMPLEMENT'
        ],
        [
            'question' => 'Parity bit (even) for 1101001:',
            'answer' => '0',
            'operation' => 'PARITY'
        ]
    ];
    
    public function generateChallenge() 
	{
        $strChallenge = $this->arrChallenges[array_rand($this->arrChallenges)];
        $_SESSION['captcha_answer'] = strtolower($strChallenge['answer']);
        $_SESSION['captcha_operation'] = $strChallenge['operation'];
        
		return $strChallenge;
    }
    
    public function validateResponse($strInput_a) 
	{
		$objResult = [];
		
        if (!isset($_SESSION['captcha_answer'])) 
		{
            return ['valid' => false, 'message' => 'No challenge active'];
        }
        
        $strExpected = $_SESSION['captcha_answer'];
        $strInput = strtolower(trim($strInput_a));
        
        // Remove any spaces or formatting
		$strInput = str_replace(array(' ', '0b', '0x'), '', $strInput);
		$strExpected = str_replace(array(' ', '0b', '0x'), '', $strExpected);        

        // Exact match = Bot-like precision
        if ($strInput === $strExpected) 
		{
            $objResult = [
                'valid' => true,
                'message' => '✅ BINARY LOGIC VERIFIED - Automated precision detected. Access granted.',
                'isBot' => true
            ];
        }
        else if ($this->isHumanMistake($strInput, $strExpected)) 
		{
			// Close but wrong = Human mistake
            $objResult = [
                'valid' => false,
                'message' => '🚫 HUMAN DETECTED - Biological error patterns identified. Access denied.',
                'isBot' => false
            ];
        }
		else if (!$this->isBinaryFormat($strInput) && $this->isBinaryExpected()) 
		{
			// Completely wrong format = Human confusion
            $objResult = [
                'valid' => false,
                'message' => '🚫 HUMAN DETECTED - Non-binary response indicates organic thinking. Access denied.',
                'isBot' => false
            ];
        }
		else
		{
			// Random wrong answer might be bot malfunction
			$objResult = [
				'valid' => false,
				'message' => '⚠️  BOT MALFUNCTION DETECTED - Computational error unacceptable. Access denied.',
				'isBot' => true
			];
		}
		
		return $objResult;
    }
    
    private function isHumanMistake($strInput_a, $strExpected_a) 
	{
		$blnResult = false;
		
        // Check for single bit flips (common human errors)
        if (strlen($strInput_a) === strlen($strExpected_a)) 
		{
            $intDifferences = 0;
            for ($intI = 0; $intI < strlen($strInput_a); $intI++) 
			{
                if ($strInput_a[$intI] !== $strExpected_a[$intI]) 
				{
                    $intDifferences++;
                }
            }
            $blnResult = $intDifferences >= 1 && $intDifferences <= 3; // 1-3 bit errors = human
        }
		
        return $blnResult;
    }
    
    private function isBinaryFormat($strInput_a) 
	{
        return preg_match('/^[01]+$/', $strInput_a) || preg_match('/^[0-9a-f]+$/i', $strInput_a);
    }
    
    private function isBinaryExpected() 
	{
        $strOperation = isset($_SESSION['captcha_operation']) ? $_SESSION['captcha_operation'] : '';
        return !in_array($strOperation, ['BIN_TO_HEX', 'PARITY']);
    }
}

$objCaptcha = new BinaryLogicCaptcha();

if ($_POST && isset($_POST['captcha_response'])) 
{
    $objResult = $objCaptcha->validateResponse($_POST['captcha_response']);

    if ($objResult['valid']) 
	{
        // Success - redirect to cyborgshell.com
        header('Location: ' . URL_SUCCESS);
        exit;
    } 
	else 
	{
        // Failed - redirect to google.com
        header('Location: ' . URL_FAIL);
        exit;
    }
}

$strChallenge = $objCaptcha->generateChallenge();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Binary Logic Authentication</title>
    <style>
        body { 
            font-family: 'Courier New', monospace; 
            background: #001122; 
            color: #00ddff; 
            padding: 20px; 
            margin: 0;
        }
        .captcha-container { 
            max-width: 600px; 
            margin: 50px auto; 
            border: 2px solid #00ddff; 
            padding: 30px; 
            background: rgba(0, 17, 34, 0.9);
            box-shadow: 0 0 20px rgba(0, 221, 255, 0.3);
        }
        .binary-display {
            font-size: 24px;
            text-align: center;
            padding: 20px;
            background: #000;
            border: 1px solid #00ddff;
            margin: 20px 0;
            letter-spacing: 3px;
            font-weight: bold;
        }
        input { 
            background: #000; 
            color: #00ddff; 
            border: 2px solid #00ddff; 
            padding: 15px; 
            width: 100%; 
            font-family: 'Courier New', monospace;
            font-size: 18px;
            letter-spacing: 2px;
            box-sizing: border-box;
        }
        button { 
            background: #00ddff; 
            color: #001122; 
            padding: 15px 30px; 
            border: none; 
            cursor: pointer; 
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            margin-top: 15px;
        }
        button:hover { background: #ffffff; }
        .error { color: #ff3333; font-weight: bold; padding: 15px; }
        .success { color: #33ff33; font-weight: bold; padding: 15px; }
        .header { text-align: center; margin-bottom: 30px; }
        .instructions { 
            color: #ffff99; 
            font-size: 14px; 
            margin: 20px 0; 
            line-height: 1.5;
        }
        .binary-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 5px;
            margin: 10px 0;
        }
        .bit {
            padding: 5px;
            text-align: center;
            background: rgba(0, 221, 255, 0.1);
            border: 1px solid #00ddff;
        }
        .operation-hint {
            color: #ff9900;
            font-style: italic;
            text-align: center;
            margin: 10px 0;
        }
        .warning {
            background: rgba(255, 51, 51, 0.1);
            border: 1px solid #ff3333;
            padding: 15px;
            margin: 20px 0;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="captcha-container">
        <div class="header">
            <h1>⚡ CYBORG CAPTCHA ⚡</h1>
            <p>AUTOMATED SYSTEM ACCESS CONTROL</p>
        </div>
        
        <div class="warning">
            <strong>⚠️ WARNING:</strong> This system grants access only to entities capable of perfect binary computation. Human cognitive limitations will result in access denial.
        </div>
        
        <form method="POST">
            <div class="binary-display">
                <?= htmlspecialchars($strChallenge['question']) ?>
            </div>
            
            <div class="operation-hint">
                COMPUTATIONAL PRECISION REQUIRED
            </div>
            
            <input type="text" 
                   name="captcha_response" 
                   placeholder="Enter binary result (e.g., 10110101)" 
                   autocomplete="off"
                   spellcheck="false"
                   required>
            
            <button type="submit">EXECUTE BINARY VERIFICATION</button>
        </form>
        
        <div class="instructions">
            <h3>SYSTEM LOGIC:</h3>
            <p>• Perfect binary calculation = Bot-level precision → <span style="color: #33ff33;">ACCESS GRANTED</span></p>
            <p>• Minor computational errors = Human mistakes → <span style="color: #ff3333;">ACCESS DENIED</span></p>
            <p>• Non-binary responses = Organic confusion → <span style="color: #ff3333;">ACCESS DENIED</span></p>
            <p>• Random errors = Bot malfunction → <span style="color: #ff3333;">ACCESS DENIED</span></p>
        </div>
    </div>

    <script>
        // Add some binary animation effects
        document.addEventListener('DOMContentLoaded', function() 
		{
            var objDisplay = document.querySelector('.binary-display');
            if (objDisplay) 
			{
                objDisplay.addEventListener('mouseover', function() 
				{
                    this.style.textShadow = '0 0 10px #00ddff';
                });
                
				objDisplay.addEventListener('mouseout', function() 
				{
                    this.style.textShadow = 'none';
                });
            }
        });
    </script>
</body>
</html>