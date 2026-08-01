<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Part;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Mandatory Users
        $owner = User::create([
            'name'     => 'Pak Hendra',
            'email'    => 'hendra@jayamotor.id',
            'password' => Hash::make('password'),
            'role'     => 'owner',
        ]);

        $cashier = User::create([
            'name'     => 'Mbak Rina',
            'email'    => 'rina@jayamotor.id',
            'password' => Hash::make('password'),
            'role'     => 'cashier',
        ]);

        $sarno = User::create([
            'name'     => 'Pak Sarno (Kelistrikan)',
            'email'    => 'sarno@jayamotor.id',
            'password' => Hash::make('password'),
            'role'     => 'mechanic',
        ]);

        $junior = User::create([
            'name'     => 'Junior Mechanic',
            'email'    => 'junior@jayamotor.id',
            'password' => Hash::make('password'),
            'role'     => 'mechanic',
        ]);

        // 2. Seed Customer Rental Owner with 4 Vehicles
        $rentalOwner = Customer::create([
            'name'            => 'Pemilik Rental Motor (Pak Budi)',
            'phone'           => '081234567890',
            'is_rental_owner' => true,
        ]);

        Vehicle::create(['customer_id' => $rentalOwner->id, 'plate_number' => 'B 1111 REN', 'model' => 'Honda Vario 125']);
        Vehicle::create(['customer_id' => $rentalOwner->id, 'plate_number' => 'B 2222 REN', 'model' => 'Yamaha NMAX 155']);
        Vehicle::create(['customer_id' => $rentalOwner->id, 'plate_number' => 'B 3333 REN', 'model' => 'Honda Beat 110']);
        Vehicle::create(['customer_id' => $rentalOwner->id, 'plate_number' => 'B 4444 REN', 'model' => 'Yamaha Aerox 155']);

        // Seed Regular Customer Bu Tuti
        $buTuti = Customer::create([
            'name'            => 'Bu Tuti',
            'phone'           => '089876543210',
            'is_rental_owner' => false,
        ]);

        Vehicle::create(['customer_id' => $buTuti->id, 'plate_number' => 'B 5555 TUT', 'model' => 'Honda Scoopy']);

        // 3. Seed Mandatory Master Parts & Services
        Part::create([
            'code'              => 'PART-OIL-DRUM',
            'name'              => 'Oli Engine Drum (Sell per Liter)',
            'purchase_unit'     => 'Drum',
            'sell_unit'         => 'Liter',
            'conversion_factor' => 30.00,
            'stock_qty'         => 30.00, // Starts with 30.00 Liters
            'buy_price'         => 900000.00, // 1 drum buy price
            'sell_price'        => 45000.00,  // sell price per liter
        ]);

        Part::create([
            'code'              => 'PART-PAD-01',
            'name'              => 'Kampas Rem Depan Genuine',
            'purchase_unit'     => 'Pcs',
            'sell_unit'         => 'Pcs',
            'conversion_factor' => 1.00,
            'stock_qty'         => 10.00,
            'buy_price'         => 30000.00,
            'sell_price'        => 45000.00,
        ]);

        Service::create([
            'name'                      => 'Jasa Perbaikan Kelistrikan (Pak Sarno)',
            'price'                     => 150000.00,
            'default_commission_amount' => 35000.00,
        ]);

        Service::create([
            'name'                      => 'Jasa Ganti Oli',
            'price'                     => 20000.00,
            'default_commission_amount' => 5000.00,
        ]);

        Service::create([
            'name'                      => 'Jasa Servis Karburator/Rutin',
            'price'                     => 60000.00,
            'default_commission_amount' => 15000.00,
        ]);

        // 4. Seed Initial Demo Work Order for Top Mechanic & Dashboard
        $demoVehicle = Vehicle::first();
        if ($demoVehicle) {
            $wo = \App\Models\WorkOrder::create([
                'wo_number'        => 'WO-SEED-001',
                'vehicle_id'       => $demoVehicle->id,
                'initial_estimate' => 206000.00,
                'final_cost'       => 206000.00,
                'status'           => 'completed',
            ]);

            \App\Models\WorkOrderItem::create([
                'work_order_id'     => $wo->id,
                'mechanic_id'       => $sarno->id,
                'item_type'         => 'service',
                'item_name'         => 'Jasa Perbaikan Kelistrikan (Pak Sarno)',
                'qty'               => 1,
                'sell_price'        => 150000.00,
                'commission_amount' => 35000.00,
                'subtotal'          => 150000.00,
            ]);

            \App\Models\WorkOrderItem::create([
                'work_order_id'     => $wo->id,
                'mechanic_id'       => $junior->id,
                'item_type'         => 'service',
                'item_name'         => 'Jasa Ganti Oli',
                'qty'               => 1,
                'sell_price'        => 20000.00,
                'commission_amount' => 5000.00,
                'subtotal'          => 20000.00,
            ]);

            \App\Models\WorkOrderItem::create([
                'work_order_id'     => $wo->id,
                'mechanic_id'       => $junior->id,
                'item_type'         => 'inventory',
                'item_name'         => 'Oli Engine Drum (Sell per Liter)',
                'qty'               => 0.8,
                'sell_price'        => 45000.00,
                'commission_amount' => 5000.00,
                'subtotal'          => 36000.00,
            ]);

            \App\Models\Invoice::create([
                'invoice_number' => 'INV-SEED-001',
                'work_order_id'  => $wo->id,
                'customer_id'    => $demoVehicle->customer_id,
                'total_amount'   => 206000.00,
                'paid_amount'    => 206000.00,
                'balance_due'    => 0,
                'status'         => 'paid',
            ]);
        }
    }
}
