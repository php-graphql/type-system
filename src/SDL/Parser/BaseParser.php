<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\SDL\Parser;

use Railt\Lexer\Factory;
use Railt\Lexer\LexerInterface;
use Railt\Parser\Grammar;
use Railt\Parser\Driver\Llk;
use Railt\Parser\ParserInterface;
use Railt\Parser\Rule\Alternation;
use Railt\Parser\Rule\Concatenation;
use Railt\Parser\Rule\Repetition;
use Railt\Parser\Rule\Terminal;
use Railt\Parser\GrammarInterface;

/**
 * --- DO NOT EDIT THIS FILE ---
 *
 * Class BaseParser has been auto-generated.
 * Generated at: 15-01-2019 18:19:36
 *
 * --- DO NOT EDIT THIS FILE ---
 */
class BaseParser extends Llk
{
    public const T_NON_NULL = 'T_NON_NULL';
    public const T_VAR = 'T_VAR';
    public const T_PARENTHESIS_OPEN = 'T_PARENTHESIS_OPEN';
    public const T_PARENTHESIS_CLOSE = 'T_PARENTHESIS_CLOSE';
    public const T_THREE_DOTS = 'T_THREE_DOTS';
    public const T_COLON = 'T_COLON';
    public const T_EQUAL = 'T_EQUAL';
    public const T_DIRECTIVE_AT = 'T_DIRECTIVE_AT';
    public const T_BRACKET_OPEN = 'T_BRACKET_OPEN';
    public const T_BRACKET_CLOSE = 'T_BRACKET_CLOSE';
    public const T_BRACE_OPEN = 'T_BRACE_OPEN';
    public const T_BRACE_CLOSE = 'T_BRACE_CLOSE';
    public const T_OR = 'T_OR';
    public const T_AND = 'T_AND';
    public const T_NUMBER_VALUE = 'T_NUMBER_VALUE';
    public const T_BOOL_TRUE = 'T_BOOL_TRUE';
    public const T_BOOL_FALSE = 'T_BOOL_FALSE';
    public const T_NULL = 'T_NULL';
    public const T_MULTILINE_STRING = 'T_MULTILINE_STRING';
    public const T_STRING = 'T_STRING';
    public const T_EXTENDS = 'T_EXTENDS';
    public const T_TYPE_IMPLEMENTS = 'T_TYPE_IMPLEMENTS';
    public const T_ON = 'T_ON';
    public const T_TYPE = 'T_TYPE';
    public const T_ENUM = 'T_ENUM';
    public const T_UNION = 'T_UNION';
    public const T_INTERFACE = 'T_INTERFACE';
    public const T_SCHEMA = 'T_SCHEMA';
    public const T_SCHEMA_QUERY = 'T_SCHEMA_QUERY';
    public const T_SCHEMA_MUTATION = 'T_SCHEMA_MUTATION';
    public const T_SCHEMA_SUBSCRIPTION = 'T_SCHEMA_SUBSCRIPTION';
    public const T_SCALAR = 'T_SCALAR';
    public const T_DIRECTIVE = 'T_DIRECTIVE';
    public const T_INPUT = 'T_INPUT';
    public const T_EXTEND = 'T_EXTEND';
    public const T_NAME = 'T_NAME';
    public const T_VARIABLE = 'T_VARIABLE';
    public const T_WHITESPACE = 'T_WHITESPACE';
    public const T_COMMENT = 'T_COMMENT';
    public const T_COMMA = 'T_COMMA';

    /**
     * Lexical tokens list.
     *
     * @var string[]
     */
    protected const LEXER_TOKENS = [
        self::T_NON_NULL => '!',
        self::T_VAR => '\\$',
        self::T_PARENTHESIS_OPEN => '\\(',
        self::T_PARENTHESIS_CLOSE => '\\)',
        self::T_THREE_DOTS => '\\.\\.\\.',
        self::T_COLON => ':',
        self::T_EQUAL => '=',
        self::T_DIRECTIVE_AT => '@',
        self::T_BRACKET_OPEN => '\\[',
        self::T_BRACKET_CLOSE => '\\]',
        self::T_BRACE_OPEN => '{',
        self::T_BRACE_CLOSE => '}',
        self::T_OR => '\\|',
        self::T_AND => '\\&',
        self::T_NUMBER_VALUE => '\\-?(0|[1-9][0-9]*)(\\.[0-9]+)?([eE][\\+\\-]?[0-9]+)?\\b',
        self::T_BOOL_TRUE => 'true\\b',
        self::T_BOOL_FALSE => 'false\\b',
        self::T_NULL => 'null\\b',
        self::T_MULTILINE_STRING => '"""(?:\\\\"""|(?!""").|\\s)*"""',
        self::T_STRING => '"[^"\\\\]*(\\\\.[^"\\\\]*)*"',
        self::T_EXTENDS => 'extends\\b',
        self::T_TYPE_IMPLEMENTS => 'implements\\b',
        self::T_ON => 'on\\b',
        self::T_TYPE => 'type\\b',
        self::T_ENUM => 'enum\\b',
        self::T_UNION => 'union\\b',
        self::T_INTERFACE => 'interface\\b',
        self::T_SCHEMA => 'schema\\b',
        self::T_SCHEMA_QUERY => 'query\\b',
        self::T_SCHEMA_MUTATION => 'mutation\\b',
        self::T_SCHEMA_SUBSCRIPTION => 'subscription\\b',
        self::T_SCALAR => 'scalar\\b',
        self::T_DIRECTIVE => 'directive\\b',
        self::T_INPUT => 'input\\b',
        self::T_EXTEND => 'extend\\b',
        self::T_NAME => '([_A-Za-z][_0-9A-Za-z]*)',
        self::T_VARIABLE => '(\\$[_A-Za-z][_0-9A-Za-z]*)',
        self::T_WHITESPACE => '[\\xfe\\xff|\\x20|\\x09|\\x0a|\\x0d]+',
        self::T_COMMENT => '#[^\\n]*',
        self::T_COMMA => ',',
    ];

    /**
     * List of skipped tokens.
     *
     * @var string[]
     */
    protected const LEXER_SKIPPED_TOKENS = [
        'T_WHITESPACE',
        'T_COMMENT',
        'T_COMMA',
    ];

    /**
     * @var int
     */
    protected const LEXER_FLAGS = Factory::LOOKAHEAD;

    /**
     * List of rule delegates.
     *
     * @var string[]
     */
    protected const PARSER_DELEGATES = [
    ];

    /**
     * Parser root rule name.
     *
     * @var string
     */
    protected const PARSER_ROOT_RULE = 'Document';

    /**
     * BaseParser constructor.
     * @throws \InvalidArgumentException
     * @throws \Railt\Lexer\Exception\BadLexemeException
     */
    public function __construct()
    {
        parent::__construct($this->bootLexer(), $this->bootGrammar());
    }

    /**
     * @return LexerInterface
     * @throws \InvalidArgumentException
     * @throws \Railt\Lexer\Exception\BadLexemeException
     */
    protected function bootLexer(): LexerInterface
    {
        return Factory::create(static::LEXER_TOKENS, static::LEXER_SKIPPED_TOKENS, static::LEXER_FLAGS);
    }

    /**
     * @return GrammarInterface
     */
    protected function bootGrammar(): GrammarInterface
    {
        return new Grammar($this->bootRules(), static::PARSER_ROOT_RULE, static::PARSER_DELEGATES);
    }

    /**
     * @return array|\Railt\Parser\Rule\Rule[]
     */
    protected function bootRules(): array
    {
        return [
        new Repetition(0, 0, -1, 'Directive', null), 
            new Repetition(1, 0, -1, 'Definition', null), 
            (new Concatenation('Document', [0, 1], 'Document'))->setDefaultId('Document'), 
            new Concatenation(3, ['DirectiveDefinition'], null), 
            new Alternation('Definition', ['ObjectDefinition', 'InterfaceDefinition', 'EnumDefinition', 'UnionDefinition', 'SchemaDefinition', 'ScalarDefinition', 'InputDefinition', 'ExtendDefinition', 3], null), 
            new Terminal(5, 'T_BOOL_TRUE', true), 
            new Terminal(6, 'T_BOOL_FALSE', true), 
            new Terminal(7, 'T_NULL', true), 
            new Alternation('ValueKeyword', [5, 6, 7], null), 
            new Terminal(9, 'T_ON', true), 
            new Terminal(10, 'T_TYPE', true), 
            new Terminal(11, 'T_TYPE_IMPLEMENTS', true), 
            new Terminal(12, 'T_ENUM', true), 
            new Terminal(13, 'T_UNION', true), 
            new Terminal(14, 'T_INTERFACE', true), 
            new Terminal(15, 'T_SCHEMA', true), 
            new Terminal(16, 'T_SCHEMA_QUERY', true), 
            new Terminal(17, 'T_SCHEMA_MUTATION', true), 
            new Terminal(18, 'T_SCHEMA_SUBSCRIPTION', true), 
            new Terminal(19, 'T_SCALAR', true), 
            new Terminal(20, 'T_DIRECTIVE', true), 
            new Terminal(21, 'T_INPUT', true), 
            new Terminal(22, 'T_EXTEND', true), 
            new Alternation('Keyword', [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22], null), 
            new Terminal('Number', 'T_NUMBER_VALUE', true), 
            new Terminal('Nullable', 'T_NULL', true), 
            new Terminal(26, 'T_BOOL_TRUE', true), 
            new Terminal(27, 'T_BOOL_FALSE', true), 
            new Alternation('Boolean', [26, 27], null), 
            new Terminal(29, 'T_MULTILINE_STRING', true), 
            new Terminal(30, 'T_STRING', true), 
            new Alternation('String', [29, 30], null), 
            new Terminal(32, 'T_NAME', true), 
            new Concatenation(33, ['ValueKeyword'], null), 
            new Alternation('Word', [32, 33], null), 
            new Terminal(35, 'T_SCHEMA_QUERY', true), 
            new Concatenation(36, [35], 'Name'), 
            new Terminal(37, 'T_SCHEMA_MUTATION', true), 
            new Concatenation(38, [37], 'Name'), 
            new Terminal(39, 'T_SCHEMA_SUBSCRIPTION', true), 
            new Concatenation(40, [39], 'Name'), 
            new Concatenation(41, ['Word'], null), 
            new Concatenation(42, [41], 'Name'), 
            (new Alternation('Name', [36, 38, 40, 42], null))->setDefaultId('Name'), 
            new Alternation(44, ['String', 'Word', 'Keyword'], null), 
            new Concatenation('Key', [44], 'Name'), 
            new Alternation(46, ['String', 'Number', 'Nullable', 'Keyword', 'Object', 'List', 'Word'], null), 
            new Concatenation('Value', [46], 'Value'), 
            new Concatenation('ValueDefinition', ['ValueDefinitionResolver'], null), 
            new Terminal(49, 'T_NON_NULL', true), 
            new Repetition(50, 0, 1, 49, null), 
            new Concatenation(51, ['ValueListDefinition', 50], 'List'), 
            new Terminal(52, 'T_NON_NULL', true), 
            new Repetition(53, 0, 1, 52, null), 
            new Concatenation(54, ['ValueScalarDefinition', 53], 'Type'), 
            new Alternation('ValueDefinitionResolver', [51, 54], null), 
            new Terminal(56, 'T_BRACKET_OPEN', false), 
            new Terminal(57, 'T_NON_NULL', true), 
            new Repetition(58, 0, 1, 57, null), 
            new Concatenation(59, ['ValueScalarDefinition', 58], 'Type'), 
            new Terminal(60, 'T_BRACKET_CLOSE', false), 
            new Concatenation('ValueListDefinition', [56, 59, 60], null), 
            new Concatenation(62, ['Word'], null), 
            new Alternation('ValueScalarDefinition', ['Keyword', 62], null), 
            new Terminal(64, 'T_BRACE_OPEN', false), 
            new Repetition(65, 0, -1, 'ObjectPair', null), 
            new Terminal(66, 'T_BRACE_CLOSE', false), 
            new Concatenation('Object', [64, 65, 66], 'Object'), 
            new Terminal(68, 'T_COLON', false), 
            new Concatenation('ObjectPair', ['Key', 68, 'Value'], 'ObjectPair'), 
            new Terminal(70, 'T_BRACKET_OPEN', false), 
            new Repetition(71, 0, -1, 'Value', null), 
            new Terminal(72, 'T_BRACKET_CLOSE', false), 
            new Concatenation('List', [70, 71, 72], 'List'), 
            new Terminal(74, 'T_MULTILINE_STRING', true), 
            new Concatenation('Documentation', [74], 'Description'), 
            new Repetition(76, 0, 1, 'Documentation', null), 
            new Terminal(77, 'T_SCHEMA', true), 
            new Repetition(78, 0, 1, 'Name', null), 
            new Repetition(79, 0, -1, 'Directive', null), 
            new Terminal(80, 'T_BRACE_OPEN', false), 
            new Terminal(81, 'T_BRACE_CLOSE', false), 
            (new Concatenation('SchemaDefinition', [76, 77, 78, 79, 80, 'SchemaDefinitionBody', 81], 'SchemaDefinition'))->setDefaultId('SchemaDefinition'), 
            new Alternation(83, ['SchemaDefinitionQuery', 'SchemaDefinitionMutation', 'SchemaDefinitionSubscription'], null), 
            new Repetition('SchemaDefinitionBody', 0, -1, 83, null), 
            new Repetition(85, 0, 1, 'Documentation', null), 
            new Terminal(86, 'T_SCHEMA_QUERY', false), 
            new Terminal(87, 'T_COLON', false), 
            new Concatenation('SchemaDefinitionQuery', [85, 86, 87, 'SchemaDefinitionFieldValue'], 'Query'), 
            new Repetition(89, 0, 1, 'Documentation', null), 
            new Terminal(90, 'T_SCHEMA_MUTATION', false), 
            new Terminal(91, 'T_COLON', false), 
            new Concatenation('SchemaDefinitionMutation', [89, 90, 91, 'SchemaDefinitionFieldValue'], 'Mutation'), 
            new Repetition(93, 0, 1, 'Documentation', null), 
            new Terminal(94, 'T_SCHEMA_SUBSCRIPTION', false), 
            new Terminal(95, 'T_COLON', false), 
            new Concatenation('SchemaDefinitionSubscription', [93, 94, 95, 'SchemaDefinitionFieldValue'], 'Subscription'), 
            new Repetition(97, 0, -1, 'Directive', null), 
            new Concatenation('SchemaDefinitionFieldValue', ['ValueDefinition', 97], null), 
            new Repetition(99, 0, 1, 'Documentation', null), 
            new Terminal(100, 'T_SCALAR', false), 
            new Repetition(101, 0, -1, 'Directive', null), 
            (new Concatenation('ScalarDefinition', [99, 100, 'Name', 101], 'ScalarDefinition'))->setDefaultId('ScalarDefinition'), 
            new Repetition(103, 0, 1, 'Documentation', null), 
            new Terminal(104, 'T_INPUT', false), 
            new Repetition(105, 0, -1, 'Directive', null), 
            new Terminal(106, 'T_BRACE_OPEN', false), 
            new Repetition(107, 0, -1, 'InputDefinitionField', null), 
            new Terminal(108, 'T_BRACE_CLOSE', false), 
            (new Concatenation('InputDefinition', [103, 104, 'Name', 105, 106, 107, 108], 'InputDefinition'))->setDefaultId('InputDefinition'), 
            new Repetition(110, 0, 1, 'Documentation', null), 
            new Terminal(111, 'T_COLON', false), 
            new Repetition(112, 0, 1, 'InputDefinitionDefaultValue', null), 
            new Repetition(113, 0, -1, 'Directive', null), 
            new Concatenation(114, ['Key', 111, 'ValueDefinition', 112, 113], null), 
            new Concatenation('InputDefinitionField', [110, 114], 'Argument'), 
            new Terminal(116, 'T_EQUAL', false), 
            new Concatenation(117, ['Value'], null), 
            new Concatenation('InputDefinitionDefaultValue', [116, 117], null), 
            new Repetition(119, 0, 1, 'Documentation', null), 
            new Terminal(120, 'T_EXTEND', false), 
            new Concatenation(121, ['ObjectDefinition'], 'ExtendDefinition'), 
            new Concatenation(122, ['InterfaceDefinition'], 'ExtendDefinition'), 
            new Concatenation(123, ['EnumDefinition'], 'ExtendDefinition'), 
            new Concatenation(124, ['UnionDefinition'], 'ExtendDefinition'), 
            new Concatenation(125, ['SchemaDefinition'], 'ExtendDefinition'), 
            new Concatenation(126, ['ScalarDefinition'], 'ExtendDefinition'), 
            new Concatenation(127, ['InputDefinition'], 'ExtendDefinition'), 
            new Concatenation(128, ['DirectiveDefinition'], 'ExtendDefinition'), 
            new Alternation(129, [121, 122, 123, 124, 125, 126, 127, 128], null), 
            (new Concatenation('ExtendDefinition', [119, 120, 129], null))->setDefaultId('ExtendDefinition'), 
            new Repetition(131, 0, 1, 'Documentation', null), 
            new Terminal(132, 'T_DIRECTIVE', false), 
            new Terminal(133, 'T_DIRECTIVE_AT', false), 
            new Repetition(134, 0, -1, 'DirectiveDefinitionArguments', null), 
            new Terminal(135, 'T_ON', false), 
            new Repetition(136, 1, -1, 'DirectiveDefinitionTargets', null), 
            (new Concatenation('DirectiveDefinition', [131, 132, 133, 'Name', 134, 135, 136], 'DirectiveDefinition'))->setDefaultId('DirectiveDefinition'), 
            new Terminal(138, 'T_PARENTHESIS_OPEN', false), 
            new Repetition(139, 0, -1, 'DirectiveDefinitionArgument', null), 
            new Terminal(140, 'T_PARENTHESIS_CLOSE', false), 
            new Concatenation('DirectiveDefinitionArguments', [138, 139, 140], null), 
            new Repetition(142, 0, 1, 'Documentation', null), 
            new Terminal(143, 'T_COLON', false), 
            new Repetition(144, 0, 1, 'DirectiveDefinitionDefaultValue', null), 
            new Repetition(145, 0, -1, 'Directive', null), 
            new Concatenation('DirectiveDefinitionArgument', [142, 'Key', 143, 'ValueDefinition', 144, 145], 'Argument'), 
            new Terminal(147, 'T_OR', false), 
            new Concatenation(148, [147, 'Key'], null), 
            new Repetition(149, 0, -1, 148, null), 
            new Concatenation('DirectiveDefinitionTargets', ['Key', 149], 'Target'), 
            new Terminal(151, 'T_EQUAL', false), 
            new Concatenation(152, ['Value'], null), 
            new Concatenation('DirectiveDefinitionDefaultValue', [151, 152], null), 
            new Repetition(154, 0, 1, 'Documentation', null), 
            new Terminal(155, 'T_TYPE', false), 
            new Repetition(156, 0, 1, 'ObjectDefinitionImplements', null), 
            new Repetition(157, 0, -1, 'Directive', null), 
            new Terminal(158, 'T_BRACE_OPEN', false), 
            new Repetition(159, 0, -1, 'ObjectDefinitionField', null), 
            new Terminal(160, 'T_BRACE_CLOSE', false), 
            (new Concatenation('ObjectDefinition', [154, 155, 'Name', 156, 157, 158, 159, 160], 'ObjectDefinition'))->setDefaultId('ObjectDefinition'), 
            new Terminal(162, 'T_TYPE_IMPLEMENTS', false), 
            new Repetition(163, 0, -1, 'Key', null), 
            new Terminal(164, 'T_AND', false), 
            new Concatenation(165, [164, 'Key'], null), 
            new Repetition(166, 0, 1, 165, null), 
            new Concatenation('ObjectDefinitionImplements', [162, 163, 166], 'Implements'), 
            new Repetition(168, 0, 1, 'Documentation', null), 
            new Repetition(169, 0, 1, 'Arguments', null), 
            new Terminal(170, 'T_COLON', false), 
            new Concatenation(171, ['Key', 169, 170, 'ObjectDefinitionFieldValue'], null), 
            new Concatenation('ObjectDefinitionField', [168, 171], 'Field'), 
            new Repetition(173, 0, -1, 'Directive', null), 
            new Concatenation('ObjectDefinitionFieldValue', ['ValueDefinition', 173], null), 
            new Repetition(175, 0, 1, 'Documentation', null), 
            new Terminal(176, 'T_INTERFACE', false), 
            new Repetition(177, 0, -1, 'Directive', null), 
            new Terminal(178, 'T_BRACE_OPEN', false), 
            new Repetition(179, 0, -1, 'InterfaceDefinitionBody', null), 
            new Terminal(180, 'T_BRACE_CLOSE', false), 
            (new Concatenation('InterfaceDefinition', [175, 176, 'Name', 177, 178, 179, 180], 'InterfaceDefinition'))->setDefaultId('InterfaceDefinition'), 
            new Terminal(182, 'T_COLON', false), 
            new Repetition(183, 0, -1, 'Directive', null), 
            new Concatenation(184, ['InterfaceDefinitionFieldKey', 182, 'ValueDefinition', 183], null), 
            new Concatenation('InterfaceDefinitionBody', [184], 'Field'), 
            new Repetition(186, 0, 1, 'Documentation', null), 
            new Repetition(187, 0, 1, 'Arguments', null), 
            new Concatenation('InterfaceDefinitionFieldKey', [186, 'Key', 187], null), 
            new Repetition(189, 0, 1, 'Documentation', null), 
            new Terminal(190, 'T_ENUM', false), 
            new Repetition(191, 0, -1, 'Directive', null), 
            new Terminal(192, 'T_BRACE_OPEN', false), 
            new Repetition(193, 0, -1, 'EnumField', null), 
            new Terminal(194, 'T_BRACE_CLOSE', false), 
            (new Concatenation('EnumDefinition', [189, 190, 'Name', 191, 192, 193, 194], 'EnumDefinition'))->setDefaultId('EnumDefinition'), 
            new Repetition(196, 0, 1, 'Documentation', null), 
            new Repetition(197, 0, -1, 'Directive', null), 
            new Concatenation(198, ['EnumValue', 197], null), 
            new Concatenation('EnumField', [196, 198], 'Value'), 
            new Terminal(200, 'T_NAME', true), 
            new Alternation(201, [200, 'Keyword'], null), 
            new Concatenation('EnumValue', [201], 'Name'), 
            new Repetition(203, 0, 1, 'Documentation', null), 
            new Terminal(204, 'T_UNION', false), 
            new Repetition(205, 0, -1, 'Directive', null), 
            new Terminal(206, 'T_EQUAL', false), 
            new Concatenation(207, ['UnionBody'], null), 
            (new Concatenation('UnionDefinition', [203, 204, 'Name', 205, 206, 207], 'UnionDefinition'))->setDefaultId('UnionDefinition'), 
            new Terminal(209, 'T_OR', false), 
            new Repetition(210, 0, 1, 209, null), 
            new Repetition(211, 1, -1, 'UnionUnitesList', null), 
            new Concatenation('UnionBody', [210, 211], 'Relations'), 
            new Terminal(213, 'T_OR', false), 
            new Concatenation(214, [213, 'Name'], null), 
            new Repetition(215, 0, -1, 214, null), 
            new Concatenation('UnionUnitesList', ['Name', 215], null), 
            new Terminal(217, 'T_PARENTHESIS_OPEN', false), 
            new Repetition(218, 0, -1, 'ArgumentPair', null), 
            new Terminal(219, 'T_PARENTHESIS_CLOSE', false), 
            new Concatenation('Arguments', [217, 218, 219], null), 
            new Repetition(221, 0, 1, 'Documentation', null), 
            new Terminal(222, 'T_COLON', false), 
            new Repetition(223, 0, 1, 'ArgumentDefaultValue', null), 
            new Repetition(224, 0, -1, 'Directive', null), 
            new Concatenation('ArgumentPair', [221, 'Key', 222, 'ValueDefinition', 223, 224], 'Argument'), 
            new Concatenation('ArgumentValue', ['ValueDefinition'], 'Type'), 
            new Terminal(227, 'T_EQUAL', false), 
            new Concatenation(228, ['Value'], null), 
            new Concatenation('ArgumentDefaultValue', [227, 228], null), 
            new Terminal(230, 'T_DIRECTIVE_AT', false), 
            new Repetition(231, 0, 1, 'DirectiveArguments', null), 
            (new Concatenation('Directive', [230, 'Name', 231], 'Directive'))->setDefaultId('Directive'), 
            new Terminal(233, 'T_PARENTHESIS_OPEN', false), 
            new Repetition(234, 0, -1, 'DirectiveArgumentPair', null), 
            new Terminal(235, 'T_PARENTHESIS_CLOSE', false), 
            new Concatenation('DirectiveArguments', [233, 234, 235], null), 
            new Terminal(237, 'T_COLON', false), 
            new Concatenation('DirectiveArgumentPair', ['Key', 237, 'Value'], 'Argument')
        ];
    }
}
