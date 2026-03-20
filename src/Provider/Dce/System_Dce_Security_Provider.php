<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
declare (strict_types=1);
namespace Ramsey\Uuid\Provider\Dce;

use function escapeshellarg;
use function preg_split;
use const PREG_SPLIT_NO_EMPTY;
use Ramsey\Uuid\Exception\Dce_Security_Exception;
use Ramsey\Uuid\Provider\Dce_Security_Provider_Interface;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use function str_getcsv;
use function strrpos;
use function strtolower;
use function strtoupper;
use function substr;
use function trim;
/**
 * SystemDceSecurityProvider retrieves the user or group identifiers from the system
 */
class System_Dce_Security_Provider implements Dce_Security_Provider_Interface
{
    /**
     * @throws DceSecurityException if unable to get a user identifier
     *
     * @inheritDoc
     */
    public function get_uid(): Integer_Object
    {
        /** @var IntegerObject | int | float | string | null $uid */
        static $uid = null;
        if ($uid instanceof Integer_Object) {
            return $uid;
        }
        if ($uid === null) {
            $uid = $this->get_system_uid();
        }
        if ($uid === '') {
            throw new Dce_Security_Exception('Unable to get a user identifier using the system DCE Security provider; please provide a custom ' . 'identifier or use a different provider');
        }
        $uid = new Integer_Object($uid);
        return $uid;
    }
    /**
     * @throws DceSecurityException if unable to get a group identifier
     *
     * @inheritDoc
     */
    public function get_gid(): Integer_Object
    {
        /** @var IntegerObject | int | float | string | null $gid */
        static $gid = null;
        if ($gid instanceof Integer_Object) {
            return $gid;
        }
        if ($gid === null) {
            $gid = $this->get_system_gid();
        }
        if ($gid === '') {
            throw new Dce_Security_Exception('Unable to get a group identifier using the system DCE Security provider; please provide a custom ' . 'identifier or use a different provider');
        }
        $gid = new Integer_Object($gid);
        return $gid;
    }
    /**
     * Returns the UID from the system
     */
    private function get_system_uid(): string
    {
        if (!$this->has_shell_exec()) {
            return '';
        }
        return match ($this->get_os()) {
            'WIN' => $this->get_windows_uid(),
            default => trim((string) shell_exec('id -u')),
        };
    }
    /**
     * Returns the GID from the system
     */
    private function get_system_gid(): string
    {
        if (!$this->has_shell_exec()) {
            return '';
        }
        return match ($this->get_os()) {
            'WIN' => $this->get_windows_gid(),
            default => trim((string) shell_exec('id -g')),
        };
    }
    /**
     * Returns true if shell_exec() is available for use
     */
    private function has_shell_exec(): bool
    {
        return !str_contains(strtolower((string) ini_get('disable_functions')), 'shell_exec');
    }
    /**
     * Returns the PHP_OS string
     */
    private function get_os(): string
    {
        /** @var string $phpOs */
        $php_os = constant('PHP_OS');
        return strtoupper(substr($php_os, 0, 3));
    }
    /**
     * Returns the user identifier for a user on a Windows system
     *
     * Windows does not have the same concept as an effective POSIX UID for the running script. Instead, each user is
     * uniquely identified by an SID (security identifier). The SID includes three 32-bit unsigned integers that make up
     * a unique domain identifier, followed by an RID (relative identifier) that we will use as the UID. The primary
     * caveat is that this UID may not be unique to the system, since it is, instead, unique to the domain.
     *
     * @link https://www.lifewire.com/what-is-an-sid-number-2626005 What Is an SID Number?
     * @link https://learn.microsoft.com/en-us/openspecs/windows_protocols/ms-dtyp/81d92bba-d22b-4a8c-908a-554ab29148ab Well-known SID Structures
     * @link https://learn.microsoft.com/en-us/windows-server/identity/ad-ds/manage/understand-security-identifiers#well-known-sids Well-known SIDs
     * @link https://www.windows-commandline.com/get-sid-of-user/ Get SID of user
     */
    private function get_windows_uid(): string
    {
        $response = shell_exec('whoami /user /fo csv /nh');
        if ($response === null) {
            return '';
        }
        $sid = str_getcsv(trim((string) $response), escape: '\\')[1] ?? '';
        if (($last_hyphen = strrpos($sid, '-')) === false) {
            return '';
        }
        return trim(substr($sid, $last_hyphen + 1));
    }
    /**
     * Returns a group identifier for a user on a Windows system
     *
     * Since Windows does not have the same concept as an effective POSIX GID for the running script, we will get the
     * local group memberships for the user running the script. Then, we will get the SID (security identifier) for the
     * first group that appears in that list. Finally, we will return the RID (relative identifier) for the group and
     * use that as the GID.
     *
     * @link https://www.windows-commandline.com/list-of-user-groups-command-line/ List of user groups command line
     */
    private function get_windows_gid(): string
    {
        $response = shell_exec('net user %username% | findstr /b /i "Local Group Memberships"');
        if ($response === null) {
            return '';
        }
        $user_groups = preg_split('/\s{2,}/', (string) $response, -1, PREG_SPLIT_NO_EMPTY);
        $first_group = trim($user_groups[1] ?? '', "* \t\n\r\x00\v");
        if ($first_group === '') {
            return '';
        }
        $response = shell_exec('wmic group get name,sid | findstr /b /i ' . escapeshellarg($first_group));
        if ($response === null) {
            return '';
        }
        $user_group = preg_split('/\s{2,}/', (string) $response, -1, PREG_SPLIT_NO_EMPTY);
        $sid = $user_group[1] ?? '';
        if (($last_hyphen = strrpos($sid, '-')) === false) {
            return '';
        }
        return trim(substr($sid, $last_hyphen + 1));
    }
}