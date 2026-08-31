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
        // - app_menu: id_menu, menu_key, parent_id, nama_menu, route_path, icon_key, urutan,
        //             is_system_scope, is_tenant_scope, is_active
        // - app_permission: id_permission, id_menu, permission_key, action_key, is_active
        // - app_role_permission: id_role_permission, id_role, id_permission, allowed, created_at, updated_at

        $params = array();
        
        // Determine visibility based on role_scope
        if ($role_scope === 'system') {
            $scopeWhere = " m.is_system_scope = 1 ";
        } else {
            $scopeWhere = " m.is_tenant_scope = 1 ";
        }

        $sql = "
            SELECT DISTINCT
                m.id_menu,
                m.parent_id,
                m.menu_key,
                m.nama_menu,
                m.route_path,
                m.icon_key,
                m.urutan
            FROM app_menu m
            INNER JOIN app_permission p
                ON p.id_menu = m.id_menu
               AND COALESCE(p.is_active,1) = 1
            INNER JOIN app_role rr
                ON rr.role_name = ?
               AND COALESCE(rr.is_active,1) = 1
            INNER JOIN app_role_permission rp
                ON rp.id_role = rr.id_role
               AND rp.id_permission = p.id_permission
               AND COALESCE(rp.allowed,1) = 1
            WHERE
                COALESCE(m.is_active,1) = 1
                AND {$scopeWhere}
            ORDER BY COALESCE(m.parent_id,0), COALESCE(m.urutan,999), m.nama_menu
        ";

        $params[] = $role_name ?: 'staff';

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
