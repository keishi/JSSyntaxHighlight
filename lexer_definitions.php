<?php

$UnicodeLetter ='[a-zA-Z]';
$DecimalDigit = '\d';
$NonZeroDigit = '[1-9]';
$DecimalDigits = "${DecimalDigit}+";
$DecimalIntegerLiteral = "(?:0|${NonZeroDigit}${DecimalDigit}*)";
$ExponentIndicator = '[eE]';
$SignedInteger = "(?:${DecimalDigits}|\\+${DecimalDigits}|-${DecimalDigits})";
$ExponentPart = "(?:${ExponentIndicator}${SignedInteger})";
$DecimalLiteral = "${DecimalIntegerLiteral}\\.${DecimalDigits}?${ExponentPart}?|\\.${DecimalDigits}${ExponentPart}?|${DecimalIntegerLiteral}${ExponentPart}?";
$HexDigit = '[0-9a-fA-F]';
$HexIntegerLiteral = "0x${HexDigit}+|0X${HexDigit}+";
$NumericLiteral = "${DecimalLiteral}|${HexIntegerLiteral}";
$SingleEscapeCharacter = '[\'"\\bfnrtv]';
$NonEscapeCharacter = '[^\'"\\bfnrtv0-9xu]';
$CharacterEscapeSequence = "${SingleEscapeCharacter}|${NonEscapeCharacter}";
$HexEscapeSequence = "x${HexDigit}${HexDigit}";
$UnicodeEscapeSequence = "(?:u${HexDigit}${HexDigit}${HexDigit}${HexDigit})";
$EscapeSequence = "${CharacterEscapeSequence}|0|${HexEscapeSequence}|${UnicodeEscapeSequence}";
$DoubleStringCharacter = "(?:[^\"\\\\]|\\\\(?:${EscapeSequence}))";
$SingleStringCharacter = "(?:[^'\\\\]|\\\\(?:${EscapeSequence}))";
$StringLiteral = "\"${DoubleStringCharacter}*\"|'${SingleStringCharacter}*'";
$IdentifierStart = "(?:${UnicodeLetter}|[\$_]|\\\\${UnicodeEscapeSequence})";
$IdentifierPart = "(?:${IdentifierStart}|[0-9])";
$IdentifierName = "${IdentifierStart}${IdentifierPart}*";
$BackslashSequence = '(?:\\\\.)';
$RegularExpressionChar = "(?:${BackslashSequence}|[^\\\\/])";
$RegularExpressionFirstChar = "(?:${BackslashSequence}|[^\\\\*\\/])";
$RegularExpressionFlags = "${IdentifierPart}*";
$RegularExpressionChars = "${RegularExpressionChar}*";
$RegularExpressionBody = "${RegularExpressionFirstChar}${RegularExpressionChars}";
$RegularExpressionLiteral = "\\/${RegularExpressionBody}\\/${RegularExpressionFlags}";
$DivPunctuator = '\/=?';
$Punctuator = '<=|>=|===|==|!=|!==|\+\+|\-\-|<<|>>|>>>|&&|\|\||\+=|\-=|\*=|%=|<<=|>>=|>>>=|&=|\|=|^=|[{}\(\[\]\.;,<>\+\-\*%&\|\^!~\?:=]'; // Removed "\)"
$MultiLineCommentChar = '(?:[^\*]|\*[^\/])';
$MultiLineComment = '\/\*' . $MultiLineCommentChar . '*\*+\/';
$SingleLineComment = '\/\/.*';
$MultiLineCommentStart = '\/\*' . $MultiLineCommentChar . '*';
$MultiLineCommentEnd = $MultiLineCommentChar . '*\*+\/';
$SingleQuoteStringStart = "^(?:'${SingleStringCharacter}*)\\\\$";
$SingleQuoteStringMiddle = "^(?:${SingleStringCharacter}*)\\\\$";
$SingleQuoteStringEnd = "${SingleStringCharacter}*'";
$DoubleQuoteStringStart = "^(?:\"${DoubleStringCharacter}*)\\\\$";
$DoubleQuoteStringMiddle = "^(?:${DoubleStringCharacter}*)\\\\$";
$DoubleQuoteStringEnd = "${DoubleStringCharacter}*\"";
$RegExpStart = "^(?:\\/${RegularExpressionBody})\\\\$";
$RegExpMiddle = "^(?:${RegularExpressionChars})\\\\$";
$RegExpEnd = "${RegularExpressionChars}\\/${RegularExpressionFlags}";

?>