-- ========================================================================
-- Copyright (C) 2011 -- Auguria	<contact@auguria.net>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
-- ========================================================================

ALTER TABLE llx_autoaddline ADD INDEX idx_autoaddline_fk_product_base (fk_product_base);
ALTER TABLE llx_autoaddline ADD CONSTRAINT fk_product_base_id_product FOREIGN KEY (fk_product_base) REFERENCES llx_product(rowid);
