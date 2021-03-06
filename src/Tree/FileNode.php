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

namespace byrokrat\autogiro\Tree;

/**
 * Node representing a file to or from autogirot
 */
class FileNode extends Node
{
    public function __construct(LayoutNode ...$layoutNodes)
    {
        foreach ($layoutNodes as $key => $layoutNode) {
            $this->setChild((string)($key + 1), $layoutNode);
        }
    }

    public function getLineNr(): int
    {
        return $this->getChild('1')->getLineNr();
    }
}
