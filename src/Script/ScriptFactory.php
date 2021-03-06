<?php

namespace BitWasp\Bitcoin\Script;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use BitWasp\Bitcoin\Math\Math;
use BitWasp\Bitcoin\Script\Consensus\BitcoinConsensus;
use BitWasp\Bitcoin\Script\Consensus\NativeConsensus;
use BitWasp\Bitcoin\Script\Factory\OutputScriptFactory;
use BitWasp\Bitcoin\Script\Factory\P2shScriptFactory;
use BitWasp\Bitcoin\Script\Factory\ScriptCreator;
use BitWasp\Bitcoin\Script\Factory\ScriptInfoFactory;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

class ScriptFactory
{
    /**
     * @param BufferInterface|string $string
     * @return Script
     */
    public static function fromHex($string)
    {
        return self::create($string instanceof BufferInterface ? $string : Buffer::hex($string))->getScript();
    }

    /**
     * @param BufferInterface|null $buffer
     * @param Opcodes|null $opcodes
     * @param Math|null $math
     * @return ScriptCreator
     */
    public static function create(BufferInterface $buffer = null, Opcodes $opcodes = null, Math $math = null)
    {
        return new ScriptCreator($math ?: Bitcoin::getMath(), $opcodes ?: new Opcodes(), $buffer);
    }

    /**
     * @param int[]|\BitWasp\Bitcoin\Script\Interpreter\Number[]|BufferInterface[] $sequence
     * @return ScriptInterface
     */
    public static function sequence(array $sequence)
    {
        return self::create()->sequence($sequence)->getScript();
    }

    /**
     * @return OutputScriptFactory
     */
    public static function scriptPubKey()
    {
        return new OutputScriptFactory();
    }

    /**
     * @param Opcodes|null $opcodes
     * @return P2shScriptFactory
     */
    public static function p2sh(Opcodes $opcodes = null)
    {
        return new P2shScriptFactory(self::scriptPubKey(), $opcodes ?: new Opcodes());
    }

    /**
     * @param ScriptInterface $script
     * @return ScriptInfo\ScriptInfoInterface
     */
    public static function info(ScriptInterface $script)
    {
        return (new ScriptInfoFactory())->load($script);
    }

    /**
     * @param EcAdapterInterface|null $ecAdapter
     * @return NativeConsensus
     */
    public static function getNativeConsensus(EcAdapterInterface $ecAdapter = null)
    {
        return new NativeConsensus($ecAdapter ?: Bitcoin::getEcAdapter());
    }

    /**
     * @return BitcoinConsensus
     */
    public static function getBitcoinConsensus()
    {
        return new BitcoinConsensus();
    }

    /**
     * @param EcAdapterInterface|null $ecAdapter
     * @return \BitWasp\Bitcoin\Script\Consensus\ConsensusInterface
     */
    public static function consensus(EcAdapterInterface $ecAdapter = null)
    {
        if (extension_loaded('bitcoinconsensus')) {
            return self::getBitcoinConsensus();
        } else {
            return self::getNativeConsensus($ecAdapter);
        }
    }
}
