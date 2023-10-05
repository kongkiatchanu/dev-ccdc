<?php
	$payload = ''.$_GET['msdu'];

	
	$ck= substr($payload, -3);
	// P4 = int(arg[4:12],16)  #6B09A4C3, 4Bytes 32bits
	// P3 = int(arg[12:18],16) #B3CDC2 , 3Bytes 24bits
	// P2 = int(arg[18:24],16) #30AC00, 3Bytes 24bits
	// P1 = int(arg[24:34],16) #FF44622333, 5Bytes 40bits

	$P1_STR = substr($payload, 18, 24);
	$P2_STR = substr($payload, 24, 34);
	$P3_STR = substr($payload, 12, 18);
	$P4_STR = substr($payload, 2, 14);
	$P1 = hexdec($P1_STR);
	$P2 = hexdec($P2_STR);


	$location = base_convert($P4_STR,16,2);
	if(strlen($location)<56){
		$f = 56 - strlen($location);
		for($i=0; $i<$f; $i++){
			$location = '0'.$location;
		}
	}
	$MILS2DEG = 1.0/(3600*1000);

	//$rLat = substr($location, 0, 25);
	//$rLon= substr($location, 25, 26);
	$rLat = base_convert(substr($location, 0, 25),2,10);
	$rLon = base_convert(substr($location, 25, 26),2,10);

	$lat = ($rLat * 32) * $MILS2DEG - 90.0;
	$lon = ($rLon * 32) * $MILS2DEG - 180.0;
	
	// TEMP
	$hexadecimal = substr($P1_STR,2,2);
	$bi = base_convert($hexadecimal,16,2);
	if(strlen($bi)<=7){
		$TEMP = hexdec($hexadecimal);
	}else{
		$TEMP = base_convert(substr($bi,1,7),2,10);
	}
	
	$CNTNUM = ($P1 >> 32) & 0xFF;
	$PM100 = ($P1 >> 22) & 0x3FF;
	$PM025 = ($P1 >> 12) & 0x3FF;

	  # Temperature by BME280
	$HUMID = $P1 & 0x7F;  # Humidity by BME280
	$STAND = ($P1 >> 8) & 0x0F;  # Standing status by BMI160
	
	$pm10 = $PM100;
	$pm2_5 = $PM025;
	$secret = $_POST['secret'];
	$temp = $TEMP;
    $standing = $STAND;
	$msdu = $_POST['msdu'];
	$src_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

	echo "msdu $payload <br/><br/>";
	
	echo "CNTNUM: $CNTNUM <br/>";
	echo "PM100: $PM100<br/>";
	echo "PM025: $PM025<br/>";
	echo "TEMP: $TEMP<br/>";
	echo "HUMID: $HUMID<br/>";
	echo "STAND: $STAND<br/>";
	echo "Lat: $lat<br/>";
	echo "Lon: $lon<br/>";
	
	
	
	
	exit;


 

# -*- coding: utf-8 -*-

 

# Modified for Thai PoC by Ando since November 25, 2022

 

# Modified on January 6, 2023 because of payload data format changed

 /*

""" README

>payload_converter-Thai_PoC.py 0xff5d3dd9bbd88b8282ac00ff44622333

Milli Second format

relative Latitude:  0xba7bb3

relative Longitude: 0x1dec45c

relative Altitude:  0x505  (1285-1000->285 [m])

<Dustboy>

Count number: 0xff  (255)

PM10 : 0x111  (273)

PM2.5: 0x222  (546)

<Sensors connected with favo-N>

Temperature (BME280): 0x0      (-40 [degree C])

Humidity (BME280): 0x33        (51 [percent RH])

Standing status (BMI160): 0x3  (if (bit0 == 1) then Standing)

Degree format: lat = 18.634337777777773 [degree], lon = 98.90200888888887 [degree]

https://www.google.co.jp/maps/@18.634337777777773,98.90200888888887,15z

 

> 

"""

import math

import sys

 

arg = sys.argv[1]   #0xFF 6B09A4C3 B3CDC2 30AC00 FF44622333

 

# Ando, Nov. 25, 2022

P4 = int(arg[4:12],16)  #6B09A4C3, 4Bytes 32bits

P3 = int(arg[12:18],16) #B3CDC2 , 3Bytes 24bits

 

# Ando, Nov. 29, 2022

P2 = int(arg[18:24],16) #30AC00, 3Bytes 24bits

P1 = int(arg[24:34],16) #FF44622333, 5Bytes 40bits

 

MILS2DEG = 1.0/(3600*1000)

 

rLat = P4 >> 7                          #32-7=25bits

rLon = (P4 & 0x7F) << (26-7) | P3 >> 5  #7+19=26Bits

 

# Ando, Nov. 29, 2022

rHgt = (P3 & 0x1F) << (14-5) | P2 >> 15 #5+9=14bits

 

# Ando, Nov. 29, 2022

CNTNUM = (P1 >> 32) & 0xFF  # Dustboy Count number

PM100  = (P1 >> 22) & 0x3FF # Dustboy 10-bit PM10

PM025  = (P1 >> 12) & 0x3FF # Dustboy 10-bit PM2.5

#PM010  =  P1        & 0x3FF # Dustboy 10-bit PM1

 

# Ando, Jan. 6, 2023

TEMP  = P2 & 0x7F # Temperature by BME280

HUMID = P1 & 0x7F # Humidity by BME280

STAND = (P1 >> 8) & 0x0F # Standing status by BMI160

 

# Ando, Nov. 29, 2022

lat = (rLat * 32) * MILS2DEG - 90.0

lon = (rLon * 32) * MILS2DEG - 180.0

Hgt = rHgt - 1000

 

# Ando, Nov. 29, 2022

#print(f"Milli Second format: rLat = {rLat} [mSec], rLon = {rLon} [mSec]")

#print(f"Altitude: rHgt = {rHgt} [m]")

#print(f"Altitude: {Hgt} [m]")

 

# Ando, Jan. 10, 2023

print("Milli Second format")

print(" relative Latitude:  %#x" % (rLat))

print(" relative Longitude: %#x" % (rLon))

#print(" relative Altitude:  %#x" % (Hgt))

# Ando, Jan. 11, 2023

print(" relative Altitude:  %#x  (%d-1000->%d [m])" % (rHgt, rHgt, Hgt))

 

print("<Dustboy>")

#print(f" Count number: {CNTNUM}")

"""

print(" Count number: %d  (%#x)" % (CNTNUM, CNTNUM))

print(" PM10 :  %d  (%#x)" % (PM100, PM100))

print(" PM2.5:  %d  (%#x)" % (PM025, PM025))

#print(" PM1  :  %d  (%#x)" % (PM010, PM010))

"""

# Ando, Jan. 11, 2023

print(" Count number: %#x  (%d)" % (CNTNUM, CNTNUM))

print(" PM10 : %#x  (%d)" % (PM100, PM100))

print(" PM2.5: %#x  (%d)" % (PM025, PM025))

 

# a20230106, added as follows

print("<Sensors connected with favo-N>")

#print(" Temperature (BME280): %d [degree C]" % (TEMP-40))

#print(" Humidity (BME280): %d [percent RH]" % (HUMID))

#print(" Standing status (BMI160): %#x" % (STAND))

# Ando, Jan. 10, 2023

print(" Temperature (BME280): %#x      (%d [degree C])" % (TEMP, TEMP-40))

print(" Humidity (BME280): %#x        (%d [percent RH])" % (HUMID, HUMID))

print(" Standing status (BMI160): %#x  (if (bit0 == 1) then Standing)" % (STAND))

 

print(f"Degree format: lat = {lat} [degree], lon = {lon} [degree]")

print(fhttps://www.google.co.jp/maps/@{lat},{lon},15z)
