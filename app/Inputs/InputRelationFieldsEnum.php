<?php

namespace App\Inputs;

enum InputRelationFieldsEnum
{
	case NONE;
	case ONLY_ADD;
	case ONLY_REMOVE;
	case ALL;
}
