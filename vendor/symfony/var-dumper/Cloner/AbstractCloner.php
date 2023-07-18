<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\VarDumper\Cloner;

use EasyCI202307\Symfony\Component\VarDumper\Caster\Caster;
use EasyCI202307\Symfony\Component\VarDumper\Exception\ThrowingCasterException;
/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static $defaultCasters = ['__PHP_Incomplete_Class' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\Caster', 'castPhpIncompleteClass'], 'EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\CutStub' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castStub'], 'EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\CutArrayStub' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castCutArray'], 'EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ConstStub' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castStub'], 'EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\EnumStub' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castEnum'], 'EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ScalarStub' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castScalar'], 'Fiber' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\FiberCaster', 'castFiber'], 'Closure' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClosure'], 'Generator' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castGenerator'], 'ReflectionType' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castType'], 'ReflectionAttribute' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castAttribute'], 'ReflectionGenerator' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castReflectionGenerator'], 'ReflectionClass' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClass'], 'ReflectionClassConstant' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClassConstant'], 'ReflectionFunctionAbstract' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castFunctionAbstract'], 'ReflectionMethod' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castMethod'], 'ReflectionParameter' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castParameter'], 'ReflectionProperty' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castProperty'], 'ReflectionReference' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castReference'], 'ReflectionExtension' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castExtension'], 'ReflectionZendExtension' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castZendExtension'], 'EasyCI202307\\Doctrine\\Common\\Persistence\\ObjectManager' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'EasyCI202307\\Doctrine\\Common\\Proxy\\Proxy' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castCommonProxy'], 'EasyCI202307\\Doctrine\\ORM\\Proxy\\Proxy' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castOrmProxy'], 'EasyCI202307\\Doctrine\\ORM\\PersistentCollection' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castPersistentCollection'], 'EasyCI202307\\Doctrine\\Persistence\\ObjectManager' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'DOMException' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castException'], 'DOMStringList' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNameList' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMImplementation' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castImplementation'], 'DOMImplementationList' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNode' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNode'], 'DOMNameSpaceNode' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNameSpaceNode'], 'DOMDocument' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDocument'], 'DOMNodeList' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNamedNodeMap' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMCharacterData' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castCharacterData'], 'DOMAttr' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castAttr'], 'DOMElement' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castElement'], 'DOMText' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castText'], 'DOMDocumentType' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDocumentType'], 'DOMNotation' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNotation'], 'DOMEntity' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castEntity'], 'DOMProcessingInstruction' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castProcessingInstruction'], 'DOMXPath' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castXPath'], 'XMLReader' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\XmlReaderCaster', 'castXmlReader'], 'ErrorException' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castErrorException'], 'Exception' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castException'], 'Error' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castError'], 'EasyCI202307\\Symfony\\Bridge\\Monolog\\Logger' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'EasyCI202307\\Symfony\\Component\\DependencyInjection\\ContainerInterface' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'EasyCI202307\\Symfony\\Component\\EventDispatcher\\EventDispatcherInterface' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'EasyCI202307\\Symfony\\Component\\HttpClient\\AmpHttpClient' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'EasyCI202307\\Symfony\\Component\\HttpClient\\CurlHttpClient' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'EasyCI202307\\Symfony\\Component\\HttpClient\\NativeHttpClient' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'EasyCI202307\\Symfony\\Component\\HttpClient\\Response\\AmpResponse' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'EasyCI202307\\Symfony\\Component\\HttpClient\\Response\\CurlResponse' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'EasyCI202307\\Symfony\\Component\\HttpClient\\Response\\NativeResponse' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'EasyCI202307\\Symfony\\Component\\HttpFoundation\\Request' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castRequest'], 'EasyCI202307\\Symfony\\Component\\Uid\\Ulid' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castUlid'], 'EasyCI202307\\Symfony\\Component\\Uid\\Uuid' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castUuid'], 'EasyCI202307\\Symfony\\Component\\VarExporter\\Internal\\LazyObjectState' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castLazyObjectState'], 'EasyCI202307\\Symfony\\Component\\VarDumper\\Exception\\ThrowingCasterException' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castThrowingCasterException'], 'EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\TraceStub' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castTraceStub'], 'EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\FrameStub' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castFrameStub'], 'EasyCI202307\\Symfony\\Component\\VarDumper\\Cloner\\AbstractCloner' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'EasyCI202307\\Symfony\\Component\\ErrorHandler\\Exception\\FlattenException' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castFlattenException'], 'EasyCI202307\\Symfony\\Component\\ErrorHandler\\Exception\\SilencedErrorContext' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castSilencedErrorContext'], 'EasyCI202307\\Imagine\\Image\\ImageInterface' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ImagineCaster', 'castImage'], 'EasyCI202307\\Ramsey\\Uuid\\UuidInterface' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\UuidCaster', 'castRamseyUuid'], 'EasyCI202307\\ProxyManager\\Proxy\\ProxyInterface' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ProxyManagerCaster', 'castProxy'], 'PHPUnit_Framework_MockObject_MockObject' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'EasyCI202307\\PHPUnit\\Framework\\MockObject\\MockObject' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'EasyCI202307\\PHPUnit\\Framework\\MockObject\\Stub' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'EasyCI202307\\Prophecy\\Prophecy\\ProphecySubjectInterface' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'EasyCI202307\\Mockery\\MockInterface' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PDO' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\PdoCaster', 'castPdo'], 'PDOStatement' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\PdoCaster', 'castPdoStatement'], 'AMQPConnection' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castConnection'], 'AMQPChannel' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castChannel'], 'AMQPQueue' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castQueue'], 'AMQPExchange' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castExchange'], 'AMQPEnvelope' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castEnvelope'], 'ArrayObject' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castArrayObject'], 'ArrayIterator' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castArrayIterator'], 'SplDoublyLinkedList' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castDoublyLinkedList'], 'SplFileInfo' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castFileInfo'], 'SplFileObject' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castFileObject'], 'SplHeap' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castHeap'], 'SplObjectStorage' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castObjectStorage'], 'SplPriorityQueue' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castHeap'], 'OuterIterator' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castOuterIterator'], 'WeakMap' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castWeakMap'], 'WeakReference' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castWeakReference'], 'Redis' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedis'], 'EasyCI202307\\Relay\\Relay' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedis'], 'RedisArray' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedisArray'], 'RedisCluster' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedisCluster'], 'DateTimeInterface' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castDateTime'], 'DateInterval' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castInterval'], 'DateTimeZone' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castTimeZone'], 'DatePeriod' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castPeriod'], 'GMP' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\GmpCaster', 'castGmp'], 'MessageFormatter' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castMessageFormatter'], 'NumberFormatter' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castNumberFormatter'], 'IntlTimeZone' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlTimeZone'], 'IntlCalendar' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlCalendar'], 'IntlDateFormatter' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlDateFormatter'], 'Memcached' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\MemcachedCaster', 'castMemcached'], 'EasyCI202307\\Ds\\Collection' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castCollection'], 'EasyCI202307\\Ds\\Map' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castMap'], 'EasyCI202307\\Ds\\Pair' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castPair'], 'EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DsPairStub' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castPairStub'], 'mysqli_driver' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\MysqliCaster', 'castMysqliDriver'], 'CurlHandle' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castCurl'], ':dba' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castDba'], ':dba persistent' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castDba'], 'GdImage' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castGd'], ':gd' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castGd'], ':pgsql large object' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLargeObject'], ':pgsql link' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLink'], ':pgsql link persistent' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLink'], ':pgsql result' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castResult'], ':process' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castProcess'], ':stream' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStream'], 'OpenSSLCertificate' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castOpensslX509'], ':OpenSSL X.509' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castOpensslX509'], ':persistent stream' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStream'], ':stream-context' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStreamContext'], 'XmlParser' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\XmlResourceCaster', 'castXml'], ':xml' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\XmlResourceCaster', 'castXml'], 'RdKafka' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castRdKafka'], 'EasyCI202307\\RdKafka\\Conf' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castConf'], 'EasyCI202307\\RdKafka\\KafkaConsumer' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castKafkaConsumer'], 'EasyCI202307\\RdKafka\\Metadata\\Broker' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castBrokerMetadata'], 'EasyCI202307\\RdKafka\\Metadata\\Collection' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castCollectionMetadata'], 'EasyCI202307\\RdKafka\\Metadata\\Partition' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castPartitionMetadata'], 'EasyCI202307\\RdKafka\\Metadata\\Topic' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicMetadata'], 'EasyCI202307\\RdKafka\\Message' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castMessage'], 'EasyCI202307\\RdKafka\\Topic' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopic'], 'EasyCI202307\\RdKafka\\TopicPartition' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicPartition'], 'EasyCI202307\\RdKafka\\TopicConf' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicConf'], 'EasyCI202307\\FFI\\CData' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\FFICaster', 'castCTypeOrCData'], 'EasyCI202307\\FFI\\CType' => ['EasyCI202307\\Symfony\\Component\\VarDumper\\Caster\\FFICaster', 'castCTypeOrCData']];
    protected $maxItems = 2500;
    protected $maxString = -1;
    protected $minDepth = 1;
    /**
     * @var array<string, list<callable>>
     */
    private $casters = [];
    /**
     * @var callable|null
     */
    private $prevErrorHandler;
    /**
     * @var mixed[]
     */
    private $classInfo = [];
    /**
     * @var int
     */
    private $filter = 0;
    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(array $casters = null)
    {
        $this->addCasters($casters ?? static::$defaultCasters);
    }
    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or objects types to a callback.
     * Types are in the key, with a callable caster for value.
     * Resource types are to be prefixed with a `:`,
     * see e.g. static::$defaultCasters.
     *
     * @param callable[] $casters A map of casters
     *
     * @return void
     */
    public function addCasters(array $casters)
    {
        foreach ($casters as $type => $callback) {
            $this->casters[$type][] = $callback;
        }
    }
    /**
     * Sets the maximum number of items to clone past the minimum depth in nested structures.
     *
     * @return void
     */
    public function setMaxItems(int $maxItems)
    {
        $this->maxItems = $maxItems;
    }
    /**
     * Sets the maximum cloned length for strings.
     *
     * @return void
     */
    public function setMaxString(int $maxString)
    {
        $this->maxString = $maxString;
    }
    /**
     * Sets the minimum tree depth where we are guaranteed to clone all the items.  After this
     * depth is reached, only setMaxItems items will be cloned.
     *
     * @return void
     */
    public function setMinDepth(int $minDepth)
    {
        $this->minDepth = $minDepth;
    }
    /**
     * Clones a PHP variable.
     *
     * @param int $filter A bit field of Caster::EXCLUDE_* constants
     * @param mixed $var
     */
    public function cloneVar($var, int $filter = 0) : Data
    {
        $this->prevErrorHandler = \set_error_handler(function ($type, $msg, $file, $line, $context = []) {
            if (\E_RECOVERABLE_ERROR === $type || \E_USER_ERROR === $type) {
                // Cloner never dies
                throw new \ErrorException($msg, 0, $type, $file, $line);
            }
            if ($this->prevErrorHandler) {
                return ($this->prevErrorHandler)($type, $msg, $file, $line, $context);
            }
            return \false;
        });
        $this->filter = $filter;
        if ($gc = \gc_enabled()) {
            \gc_disable();
        }
        try {
            return new Data($this->doClone($var));
        } finally {
            if ($gc) {
                \gc_enable();
            }
            \restore_error_handler();
            $this->prevErrorHandler = null;
        }
    }
    /**
     * Effectively clones the PHP variable.
     * @param mixed $var
     */
    protected abstract function doClone($var) : array;
    /**
     * Casts an object to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castObject(Stub $stub, bool $isNested) : array
    {
        $obj = $stub->value;
        $class = $stub->class;
        if (\strpos($class, "@anonymous\x00") !== \false) {
            $stub->class = \get_debug_type($obj);
        }
        if (isset($this->classInfo[$class])) {
            [$i, $parents, $hasDebugInfo, $fileInfo] = $this->classInfo[$class];
        } else {
            $i = 2;
            $parents = [$class];
            $hasDebugInfo = \method_exists($class, '__debugInfo');
            foreach (\class_parents($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            foreach (\class_implements($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            $parents[] = '*';
            $r = new \ReflectionClass($class);
            $fileInfo = $r->isInternal() || $r->isSubclassOf(Stub::class) ? [] : ['file' => $r->getFileName(), 'line' => $r->getStartLine()];
            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }
        $stub->attr += $fileInfo;
        $a = Caster::castObject($obj, $class, $hasDebugInfo, $stub->class);
        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
    /**
     * Casts a resource to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castResource(Stub $stub, bool $isNested) : array
    {
        $a = [];
        $res = $stub->value;
        $type = $stub->class;
        try {
            if (!empty($this->casters[':' . $type])) {
                foreach ($this->casters[':' . $type] as $callback) {
                    $a = $callback($res, $a, $stub, $isNested, $this->filter);
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
}
