<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-body">
			<div class="row">
				<div class="media-main">
					{if $user->getData()->getAvatar()}
						<a class="pull-left" href="#">
							<img class="thumb-lg img-circle bx-s" src="https://stat4market.com{$user->getData()->getAvatar()}" alt="">
						</a>
					{/if}
					<div class="info">
						<h4>{$user->getName()}</h4>
						<label class="badge bg-success">{$user->getCreated()->format('d.m.Y H:i:s')}</label>
						<p class="text-muted">{$user->getEmail()}<br/>{$user->getPhone()}</p>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<table class="table m-t-30">
					<tbody>
						<tr>
							<td>Текущий баланс</td>
							<td>{if $uf->getBalance()}{$uf->getBalance()} <i class="fa fa-ruble"></i>{else}-{/if}</td>
						</tr>
						<tr>
							<td>Сумма пополнений</td>
							<td>{if 0 < $uf->getRefillSum()}{$uf->getRefillSum()} <i class="fa fa-ruble"></i>{else}-{/if}</td>
						</tr>
						<tr>
							<td>Сумма начисленных бонусов</td>
							<td>{if 0 < $uf->getBonusSum()}{$uf->getBonusSum()} <i class="fa fa-ruble"></i>{else}-{/if}</td>
						</tr>
						<tr>
							<td>Сумма партнерских бонусов</td>
							<td>{if 0 < $uf->getBonusAffiliateSum()}{$uf->getBonusAffiliateSum()} <i class="fa fa-ruble"></i>{else}-{/if}</td>
						</tr>
						<tr>
							<td>Сумма суточных списаний</td>
							<td>{if 0 < $uf->getDailyCost()}{$uf->getDailyCost()} <i class="fa fa-ruble"></i>{else}-{/if}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
		</div>
	</div>
</div>