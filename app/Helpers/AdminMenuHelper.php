<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AdminMenuHelper
{
    /**
     * Lấy tất cả menu sections
     */
    public static function getMenuSections(): array
    {
        $config = config('menu.admin-menu-sections', []);
        $sections = [];
        
        foreach ($config as $sectionKey => $section) {
            // Kiểm tra quyền - kiểm tra cả cột role và relation roles()
            $user = Auth::user();
            $hasRole = false;
            foreach ($section['role'] as $role) {
                // Kiểm tra cột role trước (nhanh hơn)
                if (isset($user->role) && $user->role === $role) {
                    $hasRole = true;
                    break;
                }
                // Nếu không có, kiểm tra qua relation roles() từ trait
                if (method_exists($user, 'roles')) {
                    try {
                        $userRoles = $user->roles;
                        if ($userRoles && $userRoles->contains('slug', $role)) {
                            $hasRole = true;
                            break;
                        }
                    } catch (\Exception $e) {
                        // Ignore errors
                    }
                }
            }
            
            if ($hasRole) {
                $sections[$sectionKey] = [
                    'title' => $section['title'],
                ];
            }
        }
        
        return $sections;
    }
    
    /**
     * Lấy menu items từ config và xử lý dynamic data
     */
    public static function getMenuItems(string $section = 'account'): array
    {
        $config = config('menu.admin-menu-sections.' . $section, []);
        
        if (empty($config) || empty($config['items'])) {
            return [];
        }
        
        $user = Auth::user();
        // Load relations để kiểm tra trainer và referee profile
        if ($user) {
            $user->load(['hasTrainer', 'hasReferee']);
        }
        $currentRoute = request()->route() ? request()->route()->getName() : '';
        
        $items = [];
        foreach ($config['items'] as $item) {
            // Check if item has role restriction
            if (isset($item['role'])) {
                $hasItemRole = false;
                foreach ($item['role'] as $role) {
                    // Kiểm tra cột role trước (nhanh hơn)
                    if (isset($user->role) && $user->role === $role) {
                        $hasItemRole = true;
                        break;
                    }
                    // Nếu không có, kiểm tra qua relation roles() từ trait
                    if (method_exists($user, 'roles')) {
                        try {
                            $userRoles = $user->roles;
                            if ($userRoles && $userRoles->contains('slug', $role)) {
                                $hasItemRole = true;
                                break;
                            }
                        } catch (\Exception $e) {
                            // Ignore errors
                        }
                    }
                }
                if (!$hasItemRole) {
                    continue; // Skip this item if user doesn't have required role
                }
            }
            
            // Special handling for trainer/referee profile menu item
            if (isset($item['route']) && $item['route'] === 'admin.account.trainerProfile') {
                // Kiểm tra hồ sơ thực tế sử dụng relations từ User model
                $hasTrainerProfile = $user->hasTrainer !== null;
                $hasRefereeProfile = $user->hasReferee !== null;
                
                // Nếu có hồ sơ HLV -> hiển thị menu "Hồ sơ HLV"
                if ($hasTrainerProfile) {
                    $menuItem = [
                        'label' => 'Hồ sơ HLV',
                        'svg' => $item['svg'] ?? null,
                        'route' => 'admin.account.trainerProfile',
                        'url' => route('admin.account.trainerProfile'),
                        'active' => $currentRoute === 'admin.account.trainerProfile' || 
                            str_starts_with($currentRoute, 'admin.account.trainerProfile'),
                    ];
                    $items[] = $menuItem;
                }
                
                // Nếu có hồ sơ Trọng tài -> hiển thị menu "Hồ sơ Trọng tài"
                if ($hasRefereeProfile) {
                    $menuItem = [
                        'label' => 'Hồ sơ Trọng tài',
                        'svg' => $item['svg'] ?? null,
                        'route' => 'admin.account.refereeProfile',
                        'url' => route('admin.account.refereeProfile'),
                        'active' => $currentRoute === 'admin.account.refereeProfile' || 
                            str_starts_with($currentRoute, 'admin.account.refereeProfile'),
                    ];
                    $items[] = $menuItem;
                }
                
                // Skip item này vì đã được xử lý đặc biệt ở trên
                continue;
            }
            
            $menuItem = [
                'label' => $item['label'],
                'svg' => $item['svg'] ?? null,
                'url' => null,
                'route' => null,
                'active' => false,
                'onclick' => null,
            ];
            
            // Xử lý onclick (nếu có)
            if (isset($item['onclick'])) {
                $menuItem['onclick'] = $item['onclick'];
            }
            
            // Xử lý URL/Route
            if (isset($item['route'])) {
                $menuItem['route'] = $item['route'];
                $menuItem['url'] = route($item['route']);
                
                // Check active state
                $menuItem['active'] = $currentRoute === $item['route'] || 
                    str_starts_with($currentRoute, str_replace('.list', '', $item['route']));
            } elseif (isset($item['url'])) {
                $menuItem['url'] = $item['url'];
            }
            
            $items[] = $menuItem;
        }
        
        return $items;
    }
}

