-- ===================================================================
-- Copyright (C) 2024 Easya Solutions <support@easya.solutions>
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
-- along with this program; if not, write to the Free Software
-- Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
--
-- ===================================================================

ALTER TABLE llx_ecommerceng_remote_shipping_zone_methods ADD UNIQUE INDEX uk_ecommerceng_rszm(site_id, remote_zone_id, remote_instance_id, remote_method_id, entity);
--ALTER TABLE llx_ecommerceng_remote_shipping_zone_methods  ADD CONSTRAINT fk_ecommerceng_remote_rszm_warehouse_id FOREIGN KEY (warehouse_id) REFERENCES llx_entrepot(rowid);