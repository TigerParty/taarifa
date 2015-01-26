function removeGroup(group_id, incident_id) {
  $.post('/admin/reports/remove_group', { group_id: group_id, incident_id: incident_id },
    function(data){
      // Remove the group and change the status
      $('#group' + group_id).remove();
      // If there are no groups left, then change the status
      if($('*[id^="group"]').length < 1) {
        $('#incident_status').val(2);
        $.post('/admin/reports/change_status', { status: 2, incident_id: incident_id }, function() {});
      }
    });
}
