<?php
//require_once ("../lib/refresh.php")

echo "
<CiscoIPPhoneGraphicMenu>
<Title>Menu</Title>
<LocationX>-1</LocationX>
  <LocationY>-1</LocationY>
  <Width>133</Width>
  <Height>60</Height>
  <Depth>2</Depth>
  <Data>0000000000000000000000000000000000000000000000000000000000000000000000040000000000000000000000000000000000000000000000000000000000000040FF07C0FF0100000000000000000000000000000000000000000000000000000080FFBF00030D000000000000000000000000000000000000000000000000000000803FF80B0C7084410601014106900100000000000000000000000000000000000000FFE33F70D0E0CAE6282CCCE6B02900000000000000000000000000000000000000FD8FFFC1FF824302D7E4240791C001000000000000000000000000000000000000F83FFE0B03280A0A286363F486FF070000000000000000000000000094AAAA6A01D0FFF81F0CD0282860D8D800380A0000000000000000000000000040AA560195AA41FFE37F30C0A2D0C0D0D2C2C03430000000000000000000000000A41A0000004009FC8FFFC0FF8202FD010707FD427F000000000000000000000000A9020000000000D07FFE0100000000000000000000000000000000000000000040AA06000000000000FDFF01FCFFFFFFFFFFFFFFFFFF3F0000000000000000000090AA1A00000000000080BF00000000000000000000000000000000000000000000A0AA6A00502A000000000000000000000000000000000000000000000000000000A0AAAA00A4AA010000000000000000000000000000000000000000000000000000A4AAAA02A0AA060000000001000000000000000000000000000000000000000000A4AAAA0A90AA0A200000D0FF01807F000000000000400300000000000000000000A4AAAA2A80AA2A900100E0EF2FC002070000000000000D00000000000000000000A4AAAAAAA5AA2A901A00E047F4020314A001A44009297406000000000000000000A4AAAAAAAAAAAA80AA00C0FFC70FB801702D5C0E2F5DD3E6000000000000000000A0AAAAAAAAAAAA46AA0140FF1F7F40BEA0C010300C0C4403030000000000000000A0AAAAAAAAAAAAAAAA0A00FE3FFE0240CBFF43FE3030000D0C000000000000000090AAAAAAAAAAAAAAAA0600F43FFE470334074007C3C0003430000000000000000090AAAAAAAAAAAAAAAA2A00D03FFE1F2CA034340A0D030BD7C0000000000000000080AAAAAAAAAAAAAAAAAA00003F543FC0BF807FF07B0CF04B03030000000000000040AAAAAAAAAAAAAAAAAA0000F4557D000000000000000000000000000000000000002A40AAAAAAAAAAAAAA000040FF7F00FFFFFFFFFFFFFFFFFF3F00000000000000002900A0AAAAAAAAAAAA000000E02F00000000000000000000000000000000000000280000AAAAAAAAAAAA020000000000000000000000000000000000000000000000240000A4AAAAAAAA06180000000000000000000000000000000000000000000000A0000080AAAAAAAA0690000000400000000000000000000000000000000000000080010000A9AAAAAA0000000000F47F003C000F000000000000000000000000000040020000A4AAAAAA0200000000F8FF0BF0013D0000000000000000000000000000000A000080AAAAAA0200000000F802BDC00AE84006444206A400190000000000000018000000AAAAAA1A00000000F0FFF1033770C3E6F0B939780D9A0300000000000060000000A4AAAAAA02000000D0FFC71F8C914D03C7C1C131901C040000000000009000000080A6AAAA2A00000080FFC0BF308D35FE2F0707D740D31B000000000000400200000024A8AAAA0000500AFD6FFCC130D324000C1C5C030DD0010000000000000A000000400255410640AA2AF4FFE207834BC3C03170701C2C07070000000000002800000000140000A0AAAAAAC0EFC70F0C0D0DFDC2C0C1D12FF40B000000000000A00000000000000000AAAAAA02BD801F0000000000000000000000000000000000800100000000000000A9AAAA0AD0FF1FC0FFFFFFFFFFFFFFFFFFFF000000000000000A00000000000000A8AAAA2A00F80B0000000000000000000000000000000000002800000000000000A0AAAAAA000000000000000000000000000000000000000000A00000000000000090AAAAAA020000000000000000000000000000000000000000400200000000000080AAAAAA0A0010000000000000000000000000000000000000000900000000000040AAAAAA2A00FD1F000370000000000000FE02010040000000002400000000000000A8AAAAAA00FEFF021CC00100000000000A1C0C004003000000800100000000000000A9AAAA02FEC32F70000719400688001C50B890061E010519000A0000000000000040AAAA0AFC07FFC0011C9BC3E6F006D006D0E1B5780C289A03280000000000000000A0AA2AF48BFC0707701C4403C70100F9024380D230A01C0490000000000000000000AAAAE0CFF22F1CC0D11BFE2F030000380CE84BC380D21B00060000000000000000A8AA420F457F700003E028000C000CC0302C240D070AD00128000000000000000090AA0A7D15FD81030E07C7C03100B080C330F0341C2C070790010000000000000000AA2AF0BFFC03F80FF40BFDC20000FE028FBFC3D3AFF40B000A0000000000000000A0AA40FFF70700000000000000000000000000000000000028000000000000000080AA02F4FF07F0FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF0380020000000000000000A80A00FE02000000000000000000000000000000000000000A0000000000000000902A00000000000000000000000000000000000000000000A0000000000000000000AA</Data>
  <MenuItem>
  <Name>1. Contacts</Name>
  <URL>".$URLBase."contacts.php?name=".$MAC."</URL>
 </MenuItem>
 <MenuItem>
  <Name>2. Memos</Name>
  <URL>".$URLBase."memos.php?name=".$MAC."</URL>
 </MenuItem>
 <MenuItem>
  <Name>3. Status</Name>
  <URL>".$URLBase."status.php?name=".$MAC."</URL>
 </MenuItem>
</CiscoIPPhoneGraphicMenu>
";
?>
