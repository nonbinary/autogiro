<?php
/**
 * This file is part of byrokrat\autogiro.
 *
 * byrokrat\autogiro is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * byrokrat\autogiro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with byrokrat\autogiro. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2016-17 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\Record\OpeningRecordNode;
use byrokrat\autogiro\Tree\Record\ClosingRecordNode;

/**
 * Validate dates and record count in layout
 */
class LayoutVisitor extends ErrorAwareVisitor
{
    /**
     * @var string Current date from opening record
     */
    private $date;

    /**
     * @var int The number of records in layout
     */
    private $recordCount;

    /**
     * Collect date from opening record
     */
    public function beforeOpeningRecordNode(OpeningRecordNode $node)
    {
        $this->date = $node->getChild('date')->getValue();
    }

    /**
     * Validate that date in closing record matches date in opening record
     */
    public function beforeClosingRecordNode(ClosingRecordNode $node)
    {
        if ($node->getChild('date')->getValue() != $this->date) {
            $this->getErrorObject()->addError(
                "Non-matching dates in opening and closing nodes (opening: %s, closing: %s) on line %s",
                $this->date,
                $node->getChild('date')->getValue(),
                (string)$node->getLineNr()
            );
        }
    }

    /**
     * Collect the number of expected records in layout
     */
    public function afterClosingRecordNode(ClosingRecordNode $node)
    {
        $this->recordCount = (int)$node->getChild('nr_of_posts')->getValue();
    }

    /**
    * Validate that the number of records in layout matches the expected value
     */
    public function afterLayoutNode(LayoutNode $node)
    {
        if ($this->recordCount && $this->recordCount != count($node->getChildren()) - 2) {
            $this->getErrorObject()->addError(
                "Invalid record count (found: %s, expecting: %s) on line %s",
                (string)(count($node->getChildren()) - 2),
                (string)($this->recordCount),
                (string)$node->getLineNr()
            );
        }
    }
}
