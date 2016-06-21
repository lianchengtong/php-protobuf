<?php
namespace ProtobufCompiler;

require_once 'pb_proto_descriptor.php';

/**
 * Describes field
 */
class FieldDescriptor
{
    private static $_scalarTypes = array(
        \FieldDescriptorProto_Type::TYPE_DOUBLE   => \ProtobufMessage::PB_TYPE_DOUBLE,
        \FieldDescriptorProto_Type::TYPE_FLOAT    => \ProtobufMessage::PB_TYPE_FLOAT,
        \FieldDescriptorProto_Type::TYPE_INT32    => \ProtobufMessage::PB_TYPE_INT,
        \FieldDescriptorProto_Type::TYPE_INT64    => \ProtobufMessage::PB_TYPE_INT,
        \FieldDescriptorProto_Type::TYPE_UINT32   => \ProtobufMessage::PB_TYPE_INT,
        \FieldDescriptorProto_Type::TYPE_UINT64   => \ProtobufMessage::PB_TYPE_INT,
        \FieldDescriptorProto_Type::TYPE_SINT32   => \ProtobufMessage::PB_TYPE_SIGNED_INT,
        \FieldDescriptorProto_Type::TYPE_SINT64   => \ProtobufMessage::PB_TYPE_SIGNED_INT,
        \FieldDescriptorProto_Type::TYPE_FIXED32  => \ProtobufMessage::PB_TYPE_FIXED32,
        \FieldDescriptorProto_Type::TYPE_FIXED64  => \ProtobufMessage::PB_TYPE_FIXED64,
        \FieldDescriptorProto_Type::TYPE_SFIXED32 => \ProtobufMessage::PB_TYPE_FIXED32,
        \FieldDescriptorProto_Type::TYPE_SFIXED64 => \ProtobufMessage::PB_TYPE_FIXED64,
        \FieldDescriptorProto_Type::TYPE_BOOL     => \ProtobufMessage::PB_TYPE_BOOL,
        \FieldDescriptorProto_Type::TYPE_STRING   => \ProtobufMessage::PB_TYPE_STRING,
        \FieldDescriptorProto_Type::TYPE_BYTES    => \ProtobufMessage::PB_TYPE_STRING);

    private static $_scalarNativeTypes = array(
        \FieldDescriptorProto_Type::TYPE_DOUBLE   => 'float',
        \FieldDescriptorProto_Type::TYPE_FLOAT    => 'float',
        \FieldDescriptorProto_Type::TYPE_INT32    => 'int',
        \FieldDescriptorProto_Type::TYPE_INT64    => 'int',
        \FieldDescriptorProto_Type::TYPE_UINT32   => 'int',
        \FieldDescriptorProto_Type::TYPE_UINT64   => 'int',
        \FieldDescriptorProto_Type::TYPE_SINT32   => 'int',
        \FieldDescriptorProto_Type::TYPE_SINT64   => 'int',
        \FieldDescriptorProto_Type::TYPE_FIXED32  => 'int',
        \FieldDescriptorProto_Type::TYPE_FIXED64  => 'int',
        \FieldDescriptorProto_Type::TYPE_SFIXED32 => 'int',
        \FieldDescriptorProto_Type::TYPE_SFIXED64 => 'int',
        \FieldDescriptorProto_Type::TYPE_BOOL     => 'bool',
        \FieldDescriptorProto_Type::TYPE_STRING   => 'string',
        \FieldDescriptorProto_Type::TYPE_BYTES    => 'string'
    );

    private $_default;
    private $_label;
    private $_name;
    private $_namespace = null;
    private $_number;
    private $_type;
    private $_typeDescriptor = null;
    private $_typeName = null;

    /**
     * Returns default value
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * Returns label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Returns name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns camel case name
     *
     * @return string
     */
    public function getCamelCaseName()
    {
        $chunks = preg_split('/[^a-z0-9]/is', $this->getName());
        return implode('', array_map('ucfirst', $chunks));
    }

    /**
     * Returns const name
     *
     * @return string
     */
    public function getConstName()
    {
        $chunks = preg_split('/[^a-z0-9]/is', $this->getName());
        return implode('_', array_map('strtoupper', $chunks));
    }

    /**
     * Returns namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Returns number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->_number;
    }

    /**
     * Returns scalar type
     *
     * @return int
     */
    public function getScalarType()
    {
        return self::$_scalarTypes[strtolower($this->_type)];
    }

    /**
     * Returns PHP type
     *
     * @return string
     */
    public function getTypeName()
    {
        if (isset(self::$_scalarNativeTypes[strtolower($this->_type)])) {
            return self::$_scalarNativeTypes[strtolower($this->_type)];
        } else {
            if ($this->_typeDescriptor instanceof \EnumDescriptor) {
                return 'int';
            } else {
                return $this->_type;
            }
        }
    }

    public function getTypeName2()
    {
        return $this->_typeName;
    }

    /**
     * Returns true if is native type
     *
     * @return bool
     */
    public function isScalarType()
    {
        return isset(self::$_scalarNativeTypes[strtolower($this->_type)]);
    }

    /**
     * Returns type
     *
     * @return type
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Returns type descriptor
     *
     * @return string
     */
    public function getTypeDescriptor()
    {
        return $this->_typeDescriptor;
    }

    /**
     * Returns true if field is repeated
     *
     * @return bool
     */
    public function isRepeated()
    {
        return $this->_label == \FieldDescriptorProto_Label::LABEL_REPEATED;
    }

    /**
     * Returns true if is required
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->_label == \FieldDescriptorProto_Label::LABEL_REQUIRED;
    }

    /**
     * Returns true if is scalar
     *
     * @return bool
     */
    public function isProtobufScalarType()
    {
        return isset(self::$_scalarTypes[strtolower($this->_type)]);
    }

    /**
     * Returns true if is optional
     *
     * @return bool
     */
    public function isOptional()
    {
        return $this->_label == \FieldDescriptorProto_Label::LABEL_OPTIONAL;
    }

    /**
     * Sets default value
     *
     * @param mixed $default Default value
     *
     * @return null
     */
    public function setDefault($default)
    {
        $this->_default = $default;
    }

    /**
     * Sets label
     *
     * @param string $label Label
     *
     * @return null
     */
    public function setLabel($label)
    {
        $this->_label = $label;
    }

    /**
     * Sets name
     *
     * @param string $name Name
     *
     * @return null
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Sets namespace
     *
     * @param string $namespace Namespace
     *
     * @return null
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
    }

    /**
     * Sets number
     *
     * @param int $number Number
     *
     * @return null
     */
    public function setNumber($number)
    {
        $this->_number = $number;
    }

    /**
     * Sets type
     *
     * @param int $type Type
     *
     * @return null
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * Sets type descriptor
     *
     * @param int $typeDescriptor Type descriptor
     *
     * @return null
     */
    public function setTypeDescriptor($typeDescriptor)
    {
        $this->_typeDescriptor = $typeDescriptor;
    }

    /**
     * Sets type name
     *
     * @param string $typeName Type name
     *
     * @return null
     */
    public function setTypeName($typeName)
    {
        $this->_typeName = $typeName;
    }
}
