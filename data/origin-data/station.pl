#!/usr/bin/perl

my %stations={};

for (my $i=0; $i<=$#ARGV; $i++) {
   &get_all_stations($ARGV[$i]);
}

my $id= 1;
my $pkk= '';
foreach my $kk (sort keys(%stations)) {
  if ($kk !~ /HASH/) {
   my $l= length($kk);
   printf("%3d,%s,%s\n", $id, $kk, $kk);
   if ($l >= 9) { # 3 wchars
     my $prefix= substr($kk, 0, 6);
     if ($pkk =~ /^$prefix/) {
       print"XXX: duplicate city\n";
     }
   }
   $pkk= $kk;
   $id ++;
   if ($id % 100 == 0) {
     print STDERR "$id\n";
   }
  }
}

sub get_all_stations($)
{
   my ($fname) = @_;

   open IN, $fname or die "can't open $fname!\n";
   while (<IN>) {
     my @fields= split/,/;
     if ($fields[0] =~ /\d/) {
       my $st= $fields[1];
       $st =~ s/\s//g;
       if ($stations{$st} eq '') {
         $stations{$st} = $st;
       }
     }
   }
   close(IN);
}
