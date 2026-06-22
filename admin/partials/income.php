<?php
function admin_rupiah($value): string {
  return 'Rp ' . number_format((int)$value, 0, ',', '.');
}

function admin_income_rows(PDO $pdo, ?string $startDate = null, ?string $endDate = null): array {
  $where = '';
  $params = [];
  if ($startDate && $endDate) {
    $where = 'WHERE i.service_date BETWEEN ? AND ?';
    $params = [$startDate, $endDate];
  }

  $st = $pdo->prepare("
    SELECT i.*, COALESCE(SUM(it.qty * CASE
      WHEN (it.part_price + it.service_price) > 0 THEN (it.part_price + it.service_price)
      ELSE it.unit_price
    END), 0) AS subtotal,
    COALESCE(SUM(it.qty * it.part_capital_price), 0) AS total_capital
    FROM service_invoices i
    LEFT JOIN service_invoice_items it ON it.invoice_id = i.id
    {$where}
    GROUP BY i.id
    ORDER BY i.service_date DESC, i.id DESC
  ");
  $st->execute($params);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);
  foreach ($rows as &$row) {
    $row['total_income'] = max(0, (int)$row['subtotal'] - (int)$row['discount']);
    $row['net_profit'] = $row['total_income'] - (int)$row['total_capital'];
  }
  unset($row);
  return $rows;
}

function admin_income_summary(array $rows): array {
  $total = 0;
  $netProfit = 0;
  foreach ($rows as $row) {
    $total += (int)($row['total_income'] ?? 0);
    $netProfit += (int)($row['net_profit'] ?? 0);
  }
  $count = count($rows);
  return [
    'count' => $count,
    'total' => $total,
    'net_profit' => $netProfit,
    'average' => $count > 0 ? (int)round($total / $count) : 0,
  ];
}
