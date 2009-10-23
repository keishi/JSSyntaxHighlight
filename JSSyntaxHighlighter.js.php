<?php include("lexer_definitions.php"); ?>

var WebInspector = {};

WebInspector.JavaScriptSourceSyntaxHighlighter = function(table, sourceFrame)
{
    this.table = table;
    this.sourceFrame = sourceFrame;
    
    this.LEXSTATE = {
        INITIAL: 1,
        DIV_ALLOWED: 2,
    };
    this.CONTINUESTATE = {
        NONE: 0,
        COMMENT: 1,
        SINGLEQUOTESTRING: 2,
        DOUBLEQUOTESTRING: 3,
        REGEXP: 4
    };
    
    this.nonToken = "";
    this.cursor = 0;
    this.lineIndex = -1;
    this.lineCode = "";
    this.lineFragment = null;
    this.state = this.LEXSTATE.INITIAL;
    this.continueState = this.CONTINUESTATE.NONE;
    
    this.rules = [{
        pattern: /^(?:<?php echo $SingleLineComment; ?>)/,
        action: singleLineCommentAction
    }, {
        pattern: /^(?:<?php echo $MultiLineComment; ?>)/,
        action: multiLineSingleLineCommentAction
    }, {
        pattern: /^(?:<?php echo $MultiLineCommentStart; ?>)/,
        action: multiLineCommentStartAction
    }, {
        pattern: /^(?:<?php echo $MultiLineCommentEnd; ?>)/,
        action: multiLineCommentEndAction,
        continueStateCondition: this.CONTINUESTATE.COMMENT
    }, {
        pattern: /^.*/,
        action: multiLineCommentMiddleAction,
        continueStateCondition: this.CONTINUESTATE.COMMENT
    }, {
        pattern: /^(?:<?php echo $NumericLiteral; ?>)/,
        action: numericLiteralAction
    }, {
        pattern: /^(?:<?php echo $StringLiteral; ?>)/,
        action: stringLiteralAction
    }, {
        pattern: /<?php echo $SingleQuoteStringStart; ?>/,
        action: singleQuoteStringStartAction
    }, {
        pattern: /^(?:<?php echo $SingleQuoteStringEnd; ?>)/,
        action: singleQuoteStringEndAction,
        continueStateCondition: this.CONTINUESTATE.SINGLEQUOTESTRING
    }, {
        pattern: /<?php echo $SingleQuoteStringMiddle; ?>/,
        action: singleQuoteStringMiddleAction,
        continueStateCondition: this.CONTINUESTATE.SINGLEQUOTESTRING
    }, {
        pattern: /<?php echo $DoubleQuoteStringStart; ?>/,
        action: doubleQuoteStringStartAction
    }, {
        pattern: /^(?:<?php echo $DoubleQuoteStringEnd; ?>)/,
        action: doubleQuoteStringEndAction,
        continueStateCondition: this.CONTINUESTATE.DOUBLEQUOTESTRING
    }, {
        pattern: /<?php echo $DoubleQuoteStringMiddle; ?>/,
        action: doubleQuoteStringMiddleAction,
        continueStateCondition: this.CONTINUESTATE.DOUBLEQUOTESTRING
    }, {
        pattern: /^(?:<?php echo $IdentifierName; ?>)/,
        action: identOrKeywordAction,
        dontAppendNonToken: true
    }, {
        pattern: /^\)/,
        action: rightParenAction,
        dontAppendNonToken: true
    }, {
        pattern: /^(?:<?php echo $Punctuator; ?>)/,
        action: punctuatorAction,
        dontAppendNonToken: true
    }, {
        pattern: /^(?:<?php echo $DivPunctuator; ?>)/,
        action: divPunctuatorAction,
        stateCondition: this.LEXSTATE.DIV_ALLOWED,
        dontAppendNonToken: true
    }, {
        pattern: /^(?:<?php echo $RegularExpressionLiteral; ?>)/,
        action: regExpLiteralAction
    }, {
        pattern: /<?php echo $RegExpStart; ?>/,
        action: regExpStartAction
    }, {
        pattern: /^(?:<?php echo $RegExpEnd; ?>)/,
        action: regExpEndAction,
        continueStateCondition: this.CONTINUESTATE.REGEXP
    }, {
        pattern: /<?php echo $RegExpMiddle; ?>/,
        action: regExpMiddleAction,
        continueStateCondition: this.CONTINUESTATE.REGEXP
    }];
    
    function singleLineCommentAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-comment"));
    }
    
    function multiLineSingleLineCommentAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-comment"));
    }
    
    function multiLineCommentStartAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-comment"));
        this.continueState = this.CONTINUESTATE.COMMENT;
    }
    
    function multiLineCommentEndAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-comment"));
        this.continueState = this.CONTINUESTATE.NONE;
    }
    
    function multiLineCommentMiddleAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-comment"));
    }
    
    function numericLiteralAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-number"));
        this.state = this.LEXSTATE.DIV_ALLOWED;
    }
    
    function stringLiteralAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-string"));
        this.state = this.LEXSTATE.INITIAL;
    }
    
    
    function singleQuoteStringStartAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-string"));
        this.continueState = this.CONTINUESTATE.SINGLEQUOTESTRING;
    }
    
    function singleQuoteStringEndAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-string"));
        this.continueState = this.CONTINUESTATE.NONE;
    }
    
    function singleQuoteStringMiddleAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-string"));
    }
    
    function doubleQuoteStringStartAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-string"));
        this.continueState = this.CONTINUESTATE.DOUBLEQUOTESTRING;
    }
    
    function doubleQuoteStringEndAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-string"));
        this.continueState = this.CONTINUESTATE.NONE;
    }
    
    function doubleQuoteStringMiddleAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-string"));
    }
    
    function regExpLiteralAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-regexp"));
        this.state = this.LEXSTATE.INITIAL;
    }

    function regExpStartAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-regexp"));
        this.continueState = this.CONTINUESTATE.REGEXP;
    }

    function regExpEndAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-regexp"));
        this.continueState = this.CONTINUESTATE.NONE;
    }

    function regExpMiddleAction(token)
    {
        this.cursor += token.length;
        this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-regexp"));
    }
    
    function identOrKeywordAction(token)
    {
        var keywords = ["null", "true", "false", "break", "case", "catch", "const", "default", "finally", "for", "instanceof", "new", "var", "continue", "function", "return", "void", "delete", "if", "this", "do", "while", "else", "in", "switch", "throw", "try", "typeof", "with", "debugger", "class", "enum", "export", "extends", "import", "super", "get", "set"];
        this.cursor += token.length;
        if (keywords.indexOf(token) < 0) {
            this.nonToken += token;
            this.state = this.LEXSTATE.DIV_ALLOWED;
        } else {
            this.appendNonToken.call(this);
            this.lineFragment.appendChild(this.createSpan(token, "webkit-javascript-keyword"));
            this.state = this.LEXSTATE.INITIAL;
        }
    }
    
    function divPunctuatorAction(token)
    {
        this.cursor += token.length;
        this.nonToken += token;
        this.state = this.LEXSTATE.INITIAL;
    }
    
    function rightParenAction(token)
    {
        this.cursor += token.length;
        this.nonToken += token;
        this.state = this.LEXSTATE.DIV_ALLOWED;
    }
    
    function punctuatorAction(token)
    {
        this.cursor += token.length;
        this.nonToken += token;
        this.state = this.LEXSTATE.INITIAL;
    }
}

WebInspector.JavaScriptSourceSyntaxHighlighter.prototype = {
    createSpan: function(content, className)
    {
        var span = document.createElement("span");
        span.className = className;
        span.appendChild(document.createTextNode(content));
        return span;
    },

    process: function()
    {
        // Split up the work into chunks so we don't block the
        // UI thread while processing.

        var rows = this.table.rows;
        var rowsLength = rows.length;
        const tokensPerChunk = 100;
        const lineLengthLimit = 50000;
        
        var boundProcessChunk = processChunk.bind(this);
        var processChunkInterval = setInterval(boundProcessChunk, 25);
        boundProcessChunk();
        
        function processChunk()
        {
            var i;
            for (i = 0; i < tokensPerChunk; i++) {
                if (this.cursor >= this.lineCode.length)
                    moveToNextLine.call(this);
                if (this.lineIndex >= rowsLength)
                    return;
            
                if (this.cursor > lineLengthLimit) {
                    var codeFragment = this.lineCode.substring(this.cursor);
                    this.nonToken += codeFragment;
                    this.cursor += codeFragment.length;
                    return;
                }

                this.lex.call(this);
            }
        }
        
        function moveToNextLine()
        {
            this.appendNonToken.call(this);
            
            var row = rows[this.lineIndex];
            var line = row ? row.cells[1] : null;
            if (line && this.lineFragment) {
                while (line.firstChild)
                    line.removeChild(line.firstChild);
                line.appendChild(this.lineFragment);
                console.log("line children: %d", line.childNodes.length);
                this.lineFragment =null;
            }
            this.lineIndex++;
            if (this.lineIndex >= rowsLength && processChunkInterval) {
                clearInterval(processChunkInterval);
                //sourceFrame.dispatchEventToListeners("syntax highlighting complete");
                console.profileEnd("aa");
                window.after = this.table.textContent;
                console.log("result", window.before == window.after);
                
                var beforeparts = window.before.split("\n");
                var afterparts = window.after.split("\n");
                for (var i=0; i < beforeparts.length; i++) {
                    if (beforeparts[i] !== afterparts[i]) {
                        console.log("line %04d: failed", i+1);
                        console.log("before: %s", beforeparts[i]);
                        console.log("after: %s", afterparts[i]);
                    }
                };
                return;
            }
            row = rows[this.lineIndex];
            line = row ? row.cells[1] : null;
            this.lineCode = line.textContent;
            this.lineFragment = document.createDocumentFragment();
            this.cursor = 0;
            if (!line)
                moveToNextLine();
        }
    },
    
    lex: function()
    {
        var token = null;
        var codeFragment = this.lineCode.substring(this.cursor);
        
        for (var i = 0; i < this.rules.length; i++) {
            var rule = this.rules[i];
            var ruleContinueStateCondition = typeof rule.continueStateCondition === "undefined" ? this.CONTINUESTATE.NONE : rule.continueStateCondition;
            if (this.continueState === ruleContinueStateCondition) {
                if (typeof rule.stateCondition !== "undefined" && this.state !== rule.stateCondition)
                    continue;
                var match = rule.pattern.exec(codeFragment);
                if (match) {
                    token = match[0];
                    if (token) {
                        if (!rule.dontAppendNonToken)
                            this.appendNonToken.call(this);
                        return rule.action.call(this, token);
                    }
                }
            }
        }
        this.nonToken += codeFragment[0];
        this.cursor++;
    },
    
    appendNonToken: function appendNonToken() {
        if (this.nonToken.length > 0) {
            this.lineFragment.appendChild(document.createTextNode(this.nonToken));
            this.nonToken = "";
        }
    }
}