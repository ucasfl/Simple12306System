#!/usr/bin/perl

#
# This perl script checks a number of data quality problems.
#
# 1. station sequence number in a file must be in order
# 2. no duplicate or empty station names
# 3. time values are increasing
# 4. no suspiciously long stay at a station
# 5. price format
#
# To run the script:
#   $ for i in */*.csv; do ./check-data.pl $i; done
#

my $last_hour=-1;
my $last_min= -1;
my $duration_min= 0;

if ($#ARGV != 0) {
  print "Usage: $0 <train.csv>\n";
  exit(1);
}

my $fname= $ARGV[0];

my @lines= ();
my $num= 0;

open IN, $fname or die "can't open $fname!\n";
while (<IN>) {
  chomp;

  $lines[$num] = $_;
  $num ++;

}
close(IN); 

# 1. check duplicates
for (my $i=1; $i <$num; $i++) {
   if ($lines[$i] eq $lines[$i-1]) {
     print "$fname has duplicates!\n";
     exit(1);
   }
}


# 2. check the columns
#  2,    石家庄,     09:04,     09:09,       5分,        81,       232,   68/81.5,           -/-/-,           -/-

if ($num < 3) {
  print "$fname has only $num lines!\n";
  exit(1);
}

my @price=(0,0,0,0,0,0,0);

my %stations= {};
for (my $i=1; $i<$num; $i++) {
   my $cur= $lines[$i];
   $cur =~ s/\s//g;
   my @fields= split /,/, $cur;
   if ($fields[0] != $i) {
     print "$fname sequence number error $i!\n";
     exit(1);
   }
   if ($fields[1] eq '') {
     print "$fname station is empty!\n";
     exit(1);
   }
   else {
     my $s= $fields[1];
     if ($stations{$s} ne '') {
       print "$fname duplicate station $s!\n";
       exit(1);
     }
     $stations{$s} = 1;
   }
   if ($i > 1) {
     if ($fields[2] =~ /\d\d:\d\d/) {
       # ok
     }
     else {
       print "$fname end time error $fields[2]!\n";
       exit(1);
     }
     &gettime($fields[2]);
   }
   if ($i < $num-1) {
     if ($fields[3] =~ /\d\d:\d\d/) {
       # ok
     }
     else {
       print "$fname start time error $fields[3]!\n";
       exit(1);
     }
     &gettime($fields[3]);

     if ($duration_min >= 100) {
       print "$fname stayed $duration_min: $fields[2] $fields[3]!\n";
       exit(1);
     }
   }

   if ($i >= 2) {
     my @pr7= split /\//, $fields[7];
     my @pr8= split /\//, $fields[8];
     my @pr9= split /\//, $fields[9];
     if (($#pr7 != 1)||($#pr8 != 2)||($#pr9 !=1)) {
       print "$fname price format is wrong: $#pr7, $#pr8, $#pr9!\n";
       exit(1);
     }
     my @curprice = ($pr7[0], $pr7[1], $pr8[0], $pr8[1], $pr8[2], $pr9[0], $pr9[1]);
     my $curpre= 0;
     for (my $j=0; $j<7; $j++) {
        if (($curprice[$j] ne '-') && ($curprice[$j]>0)) {
          if ($price[$j] > $curprice[$j]) {
            print "$fname prices decrease at line $i!\n";
            exit(1);
          }
          if (($j!=2) && ($j!=5) && ($curpre > $curprice[$j])) {
            print "$fname price strange at line $i $curprice[$j]!\n";
            exit(1);
          }
          $price[$j] = $curprice[$j];
          $curpre = $curprice[$j];
        }
     }
     #for (my $j=0; $j<7; $j++) {
     #   print $price[$j], " ";
     #}
     #print "\n";
   }
}

sub gettime($)
{
  my ($tstr)= @_;
  if ($tstr =~ /(\d\d):(\d\d)/) {
    my $h= $1;
    my $m= $2;

    if ($last_hour >= 0) {
      $duration_min = ($h*60+$m) - ($last_hour*60+$last_min);
      if ($duration_min < 0) {
        $duration_min += 24*60;
        if ($duration_min > 12*60) {
          print "$fname $last_hour:$last_min => $h:$m\n";
          exit(1);
        }
      }
    }

    #print "$last_hour:$last_min => $h:$m\n";
    $last_hour= $h; $last_min= $m;
  }
}

