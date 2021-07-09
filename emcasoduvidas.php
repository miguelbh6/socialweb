<div align="left">
<fieldset class="classfieldset4" style="width:550px">
   <legend class="classlegend4">Em caso de dúvidas</legend>

  <div align="left">
    <p/>
      <label class="result"><?php echo $utility->getDadosPrefeitura("pre_nome"); ?></label>
    <p/>

	<!--<p/><label>Endere&ccedil;o:</label><p/>
    <p/>
      <label class="result"><?php echo $utility->getEnderecoPrefeitura(false); ?></label>
    <p/>
    <p/>
      <label class="result"></label>
    <p/>
    <p/>
      <label class="result"><?php echo $utility->getDadosPrefeitura("pre_cidade"); ?> - <?php echo $utility->getDadosPrefeitura("pre_estado"); ?></label>
    <p/>
    <p/>
      <label class="result">CEP: <?php echo $utility->getDadosPrefeitura("pre_cep"); ?></label>
    <p/>

	<p/>
    <label>Telefone:</label>
    <label class="result"><?php echo $utility->getDadosPrefeitura("pre_telefone"); ?></label>
    <p/>-->

	<p/><label class="result"><?php echo nl2br($utility->getDadosPrefeitura('pre_textoemcasoduvidas')); ?></label><p/>

  </div>
</fieldset>
</div>