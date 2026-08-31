<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_menu extends CI_Model
{
    /**
     * Ambil menu berdasarkan user login, tenant, role scope & role name.
     * Struktur output: tree parent-child untuk langsung dipakai header.
     */
    public function get_menu_tree_for_user($id_pengguna, $id_perusahaan, $role_scope, $role_name = null)
    {
        // NOTE:
        // - app_menu: id_menu, parent_id, kode_menu, nama_menu, route, icon, urutan,
        //             scope (system|tenant|both), is_active
        // - app_permission: id_permission, id_menu, permission_key, is_active
        // - app_role_permission: id_role_permission, role_name, id_permission, is_allowed, is_active

        $params = array();
        $scopeWhere = " (m.scope = 'both' OR m.scope = ?) ";
        $params[] = $role_scope;

        $sql = "
            SELECT DISTINCT
                m.id_menu,
                m.parent_id,
                m.kode_menu,
                m.nama_menu,
                m.route,
                m.icon,
                m.urutan
            FROM app_menu m
            INNER JOIN app_permission p
                ON p.id_menu = m.id_menu
               AND COALESCE(p.is_active,1) = 1
            INNER JOIN app_role_permission rp
                ON rp.id_permission = p.id_permission
               AND rp.role_name = ?
               AND COALESCE(rp.is_allowed,1) = 1
               AND COALESCE(rp.is_active,1) = 1
            WHERE
                COALESCE(m.is_active,1) = 1
                AND {$scopeWhere}
            ORDER BY COALESCE(m.parent_id,0), COALESCE(m.urutan,999), m.nama_menu
        ";

        array_unshift($params, $role_name ?: 'staff');

        $rows = $this->db->query($sql, $params)->result_array();

        if (empty($rows)) {
            return array();
        }

        $byId = array();
        foreach ($rows as $r) {
            $r['children'] = array();
            $byId[$r['id_menu']] = $r;
        }

        $tree = array();
        foreach ($byId as $id => $menu) {
            $pid = $menu['parent_id'];
            if (!empty($pid) && isset($byId[$pid])) {
                $byId[$pid]['children'][] = &$byId[$id];
            } else {
                $tree[] = &$byId[$id];
            }
        }

        return $tree;
    }
}
