{if $action eq 1024}
  <div id="help">
    <h3>Cron / Drush</h3>
    <p>Use the following command to execute the <strong>{$query.name}</strong> query via cron / drush.</p>
    <pre>drush cvapi -r {$doc_root}{if $server} -l {$server}{/if} query.execute name={$query.machine_name}</pre>
  </div>
{/if}

{if $action eq 1 or $action eq 2 or $action eq 8}
  {include file="CRM/Queryrunner/Form/QueryRunner.tpl"}
{else}

  {if $rows}

    {if $action ne 1 and $action ne 2}
      <div class="action-link">
        {crmButton q="action=add&reset=1" id="newQuery" icon="circle-plus"}{ts}Add New Query{/ts}{/crmButton}
      </div>
    {/if}

    <div id="ltype">
    {strip}
      {* handle enable/disable actions*}
      {* include file="CRM/common/enableDisableApi.tpl" ADAPTED BELOW *}

{literal}
<script type="text/javascript">
  CRM.$(function($) {
    var $a, $row, info, enabled, fieldLabel;

    function successMsg() {
      {/literal} {* client-side variable substitutions in smarty are AWKWARD! *}
      var msg = enabled ? '{ts escape="js" 1="<em>%1</em>"}%1 Disabled{/ts}' : '{ts escape="js" 1="<em>%1</em>"}%1 Enabled{/ts}'{literal};
      return ts(msg, {1: fieldLabel});
    }

    function refresh() {
      $a.trigger('crmPopupFormSuccess');
      CRM.refreshParent($row);
    }

    function save() {
      $row.closest('table').block();
      CRM.api3(info.entity, info.action, {id: info.id, field: 'is_active', value: enabled ? 0 : 1}, {success: successMsg}).done(refresh);
    }

    function enableDisable() {
      $a = $(this);
      $row = $a.closest('.crm-entity');
      info = $a.crmEditableEntity();
      fieldLabel = info.label || info.title || info.display_name || info.name || {/literal}'{ts escape="js"}Record{/ts}'{literal};
      enabled = !$row.hasClass('disabled');
      save();
      return false;
    }

    // Because this is an inline script it may get added to the document more than once, so remove handler before adding
    $('body')
      .off('.crmEnableDisable')
      .on('click.crmEnableDisable', '.action-item.crm-enable-disable', enableDisable);
  });
</script>
{/literal}

        <br/><table class="selector row-highlight">
        <tr class="columnheader">
            <th >{ts}Name (Frequency){/ts}</th>
            <th >{ts}Description{/ts}</th>
            <th >{ts}Run{/ts}</th>
            <th >{ts}Enabled{/ts}</th>
            <th ></th>
        </tr>
        {foreach from=$rows item=row}
        <tr id="query-{$row.id}" class="crm-entity {cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td class="crm-job-name"><strong><span data-field="name">{$row.name}</span></strong> ({$row.freq_text})</td>
            <td class="crm-job_name">{$row.description}</td>      
            <td class="crm-job-name">
              Last: {if $row.last_run eq null}never{else}{$row.last_run|crmDate:$config->dateformatDatetime}{/if}<br />
              Next: {if $row.run_frequency eq 6}always{elseif $row.run_frequency eq 0}never{else}{$row.next_run_date|crmDate:$config->dateformatDatetime}{/if}
            </td>
            <td id="row_{$row.id}_status" class="crm-job-is_active">{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
          <td>{$row.action|replace:'xx':$row.id}</td>
        </tr>
        {/foreach}
        </table>
    {/strip}

    {if $action ne 1 and $action ne 2}
        <div class="action-link">
          <a href="{crmURL q="action=add&reset=1"}" id="newQuery-bottom" class="button"><span><div class="icon ui-icon-circle-plus"></div>{ts}Add New Query{/ts}</span></a>
        </div>
    {/if}
</div>
{elseif $action ne 1}
    <div class="messages status no-popup">
      <div class="icon inform-icon"></div>
        {ts}There are no queries configured.{/ts}
     </div>
     <div class="action-link">
       <a href="{crmURL p='civicrm/query-runner' q="action=add&reset=1"}" id="newQuery-nojobs" class="button"><span><div class="icon ui-icon-circle-plus"></div>{ts}Add New Query{/ts}</span></a>
     </div>

{/if}
{/if}
