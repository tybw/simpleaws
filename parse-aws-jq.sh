#!/bin/bash

JSON="/tmp/$$.txt"
aws --profile profile ec2 describe-instances > $JSON

ip=`cat $JSON | jq '.Reservations[].Instances[].PublicIpAddress'`
num_instances=`echo $ip  | wc -w`
cat $JSON | jq '.Reservations[].Instances[].PrivateIpAddress'
cat $JSON | jq '.Reservations[].Instances[].State.Name'
cat $JSON | jq '.Reservations[].Instances[].ImageId'
cat $JSON | jq '.Reservations[].Instances[].InstanceId'
cat $JSON | jq '.Reservations[].Instances[].BlockDeviceMappings[].Ebs.VolumeId'
cat $JSON | jq '.Reservations[].Instances[].BlockDeviceMappings[].DeviceName'
cat $JSON | jq '.Reservations[].Instances[].Tags'
#cat $JSON | jq '.Reservations[].Instances[] | {ImageId, PublicIpAddress, PrivateIpAddress, Tags}'

rm $JSON

exit 0;
# aws3wm autoscaling describe-auto-scaling-instances
# aws3wm autoscaling describe-scaling-activities
# aws3wm autoscaling update-auto-scaling-group help
# aws3wm autoscaling set-desired-capacity --auto-scaling-group-name basic-auto-scaling-group --desired-capacity 2 --honor-cooldown

