{* This template is used for adding/configuring Queries.  *}
<h3>{if $action eq 1}{ts}New Query{/ts}{elseif $action eq 2}{ts}Edit Query{/ts}{elseif $action eq 128}{ts}Execute Query{/ts}{else}{ts}Delete Query{/ts}{/if}</h3>
<div class="crm-block crm-form-block crm-job-form-block">
 <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>

{if $action eq 8}
  <div class="messages status no-popup">
      <div class="icon inform-icon"></div>
        <strong>{$query.name}</strong><br />
        {ts}Are you sure you want to delete this query?{/ts}
  </div>
{else}
  <table class="form-layout-compressed">
    <tr>
        <td class="label">{$form.name.label}</td><td>{$form.name.html}</td>
    </tr>
    <tr>
        <td class="label">{$form.description.label}</td><td>{$form.description.html}</td>
    </tr>
    <tr>
        <td class="label">{$form.query.label}</td><td>{$form.query.html}</td>
    </tr>
    <tr>
        <td class="label">{$form.run_frequency.label}</td><td>{$form.run_frequency.html}</td>
    </tr>
    <tr>
        <td class="label">{$form.starting.label}</td><td>{$form.starting.html}</td>
    </tr>
    <tr>
      <td></td><td>{$form.is_active.html}&nbsp;{$form.is_active.label}</td>
    </tr>
  </table>
{/if}
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>

