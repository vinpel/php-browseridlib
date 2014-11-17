<?php
/**
 * Verifier
 *
 * Verifies an assertion received via HTTP POST and returns a JSON object.
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    BrowserID
 * @author     Benjamin Krämer <benjamin.kraemer@alien-scripts.de>
 * @copyright  Alien-Scripts.de Benjamin Krämer
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

// Disable error messages
//error_reporting(0);

/**
 * Include BrowserID library
 */
require_once("./lib/browserid.php");

//$assertion = $_POST['assertion'];
//$audience = $_POST['audience'];
$assertion="eyJhbGciOiJSUzI1NiJ9.eyJmeGEtZ2VuZXJhdGlvbiI6MTQxMTQwNTU1OTc2MSwiZnhhLWxhc3RBdXRoQXQiOjE0MTE0MDYxMjIsImZ4YS12ZXJpZmllZEVtYWlsIjoic3luYzNAcGVsaXNzZXQuY29tIiwicHVibGljLWtleSI6eyJhbGdvcml0aG0iOiJEUyIsInkiOiI2ZTk0ZjZiOGZmMDMzMTM2ODU3NzVlMTZjNDM1MDk5YmRiNzViMjg2Mjg2ZWM4MzRkYzg3ZjE0NWMyMDYyNDQ1MmEwOTBiZTRhOTA4ODcyNzQzZWM1NTRhNjAxYzAyYjY2MmRjNTQyNzI0MmJmNTNlMjk4NzE4YmM3NmU2ODMxYThkYjYwMDM1MzY4N2NlMjIzYzI1NDNiNTBlYjQ3M2I4NGU5NTYzMTMyMTJlM2VmZTM4NmE5ZjYzODdjOTY0NmZiOTBhZmM4NGFiN2E3MTUwNTc1NDY4MmMzOGRlNDRhNmQ2MDIzZTVjNmVhNzcwZTkzMjgxMTYwMWI1OTUwZGMyIiwicCI6ImZmNjAwNDgzZGI2YWJmYzViNDVlYWI3ODU5NGIzNTMzZDU1MGQ5ZjFiZjJhOTkyYTdhOGRhYTZkYzM0ZjgwNDVhZDRlNmUwYzQyOWQzMzRlZWVhYWVmZDdlMjNkNDgxMGJlMDBlNGNjMTQ5MmNiYTMyNWJhODFmZjJkNWE1YjMwNWE4ZDE3ZWIzYmY0YTA2YTM0OWQzOTJlMDBkMzI5NzQ0YTUxNzkzODAzNDRlODJhMThjNDc5MzM0MzhmODkxZTIyYWVlZjgxMmQ2OWM4Zjc1ZTMyNmNiNzBlYTAwMGMzZjc3NmRmZGJkNjA0NjM4YzJlZjcxN2ZjMjZkMDJlMTciLCJxIjoiZTIxZTA0ZjkxMWQxZWQ3OTkxMDA4ZWNhYWIzYmY3NzU5ODQzMDljMyIsImciOiJjNTJhNGEwZmYzYjdlNjFmZGYxODY3Y2U4NDEzODM2OWE2MTU0ZjRhZmE5Mjk2NmUzYzgyN2UyNWNmYTZjZjUwOGI5MGU1ZGU0MTllMTMzN2UwN2EyZTllMmEzY2Q1ZGVhNzA0ZDE3NWY4ZWJmNmFmMzk3ZDY5ZTExMGI5NmFmYjE3YzdhMDMyNTkzMjllNDgyOWIwZDAzYmJjNzg5NmIxNWI0YWRlNTNlMTMwODU4Y2MzNGQ5NjI2OWFhODkwNDFmNDA5MTM2YzcyNDJhMzg4OTVjOWQ1YmNjYWQ0ZjM4OWFmMWQ3YTRiZDEzOThiZDA3MmRmZmE4OTYyMzMzOTdhIn0sInByaW5jaXBhbCI6eyJlbWFpbCI6IjhiOWVmMWViMzAyNTQ3MDRiNWI4ZjY1ZGM1YWEzZmIxQGFwaS5hY2NvdW50cy5maXJlZm94LmNvbSJ9LCJpYXQiOjE0MTE0MDYxMTM1NjAsImV4cCI6MTQxMTQyNzcyMzU2MCwiaXNzIjoiYXBpLmFjY291bnRzLmZpcmVmb3guY29tIn0.TppTGXVaO4evXL-H7HFftK7vAH3vJzOTYJ-zSJOEp82oEdm0LAjxH37AfsTQ_aHffSNp1rtZB50coHrjIRrPX2Z3MbnxcNhcShZ6Pr8KLk0ZVroPLcQBXNcY7mgkW5BXaxfg11Cin3BGUPpcx8y0qV9Y-r2mf91y9AsaWJ2b62n4hO5xb_zX1hgkuP5EfmnQ6ypJIxfw4Q3r_OXPC7k9M7OP0RO1aQi9Rs21WenBjf6QYM1C13l5I_-yH3XHIAiAntEsBXTA1k-Hv8NC1VfXBSePQ0mbJRb_ugpJKHbRjHyMQLK_o1IybSNxXJWmnt9aDupeNtxdZSLB9htoq6js7g~eyJhbGciOiJEUzEyOCJ9.eyJleHAiOjIxOTk4MDYxMjMwMDAsImF1ZCI6Imh0dHBzOi8vMTkyLjE2OC4wLjQxIn0=.q0UXlk94la9NWlUbxMzNQrpRheV3RrEqJi1PgUgBh-wJv4lzO8cE0w==";
$audience='https://192.168.0.41';

echo Verifier::verify($assertion, $audience);
?>