<?php

namespace Tests\Feature;

use App\Enums\MetodePembayaran;
use App\Enums\Role;
use App\Enums\StatusPembayaran;
use App\Enums\StatusPesanan;
use App\Models\Jas;
use App\Models\Kategori;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlurPenyewaanTest extends TestCase
{
    use RefreshDatabase;

    private function buatKategori(): Kategori
    {
        return Kategori::create(['nama' => 'Formal', 'slug' => 'formal']);
    }

    private function buatJas(array $atribut = []): Jas
    {
        return Jas::create(array_merge([
            'kategori_id' => $this->buatKategori()->id,
            'nama' => 'Jas Uji',
            'deskripsi' => 'Jas untuk pengujian otomatis.',
            'ukuran' => 'S,M,L',
            'harga_sewa' => 50000,
            'stok' => 5,
            'is_aktif' => true,
        ], $atribut));
    }

    private function buatCustomer(): User
    {
        return User::factory()->create(['role' => Role::Customer]);
    }

    private function buatAdmin(): User
    {
        return User::factory()->admin()->create();
    }

    private function buatPesanan(User $user, Jas $jas, array $atribut = []): Pesanan
    {
        return Pesanan::create(array_merge([
            'user_id' => $user->id,
            'jas_id' => $jas->id,
            'tanggal_pesan' => today(),
            'tanggal_mulai' => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
            'ukuran' => 'M',
            'jumlah' => 1,
            'total_harga' => 100000,
            'status' => StatusPesanan::Pending,
        ], $atribut));
    }

    public function test_pengunjung_dapat_melihat_halaman_publik(): void
    {
        $jas = $this->buatJas();

        $this->get('/')->assertOk();
        $this->get('/katalog')->assertOk();
        $this->get('/jas/'.$jas->id)->assertOk();
    }

    public function test_customer_tidak_dapat_mengakses_area_admin(): void
    {
        $this->actingAs($this->buatCustomer())
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_dapat_mengakses_dashboard(): void
    {
        $this->actingAs($this->buatAdmin())
            ->get('/admin')
            ->assertOk();
    }

    public function test_customer_dapat_menambah_keranjang_dan_checkout(): void
    {
        $customer = $this->buatCustomer();
        $jas = $this->buatJas(['stok' => 5]);

        $this->actingAs($customer)->post('/keranjang', [
            'jas_id' => $jas->id,
            'ukuran' => 'M',
            'jumlah' => 2,
            'tanggal_mulai' => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
        ])->assertRedirect(route('keranjang.index'));

        $this->assertDatabaseHas('keranjang', [
            'user_id' => $customer->id,
            'jas_id' => $jas->id,
            'jumlah' => 2,
        ]);

        $this->actingAs($customer)->post('/checkout', ['catatan' => 'Tolong disiapkan'])
            ->assertRedirect(route('pesanan.index'));

        $this->assertDatabaseHas('pesanan', [
            'user_id' => $customer->id,
            'jas_id' => $jas->id,
            'jumlah' => 2,
            'status' => StatusPesanan::Pending->value,
        ]);

        $this->assertSame(3, $jas->fresh()->stok);
        $this->assertDatabaseCount('keranjang', 0);
    }

    public function test_admin_dapat_memverifikasi_pembayaran(): void
    {
        $customer = $this->buatCustomer();
        $jas = $this->buatJas();
        $pesanan = $this->buatPesanan($customer, $jas);

        $pembayaran = Pembayaran::create([
            'pesanan_id' => $pesanan->id,
            'metode' => MetodePembayaran::Transfer,
            'status' => StatusPembayaran::Pending,
            'tanggal_bayar' => now(),
        ]);

        $this->actingAs($this->buatAdmin())
            ->post('/admin/verifikasi/'.$pembayaran->id.'/setujui')
            ->assertRedirect();

        $this->assertSame(StatusPembayaran::Diverifikasi, $pembayaran->fresh()->status);
        $this->assertSame(StatusPesanan::Disetujui, $pesanan->fresh()->status);
    }

    public function test_customer_tidak_dapat_melihat_pesanan_orang_lain(): void
    {
        $pemilik = $this->buatCustomer();
        $lain = $this->buatCustomer();
        $pesanan = $this->buatPesanan($pemilik, $this->buatJas());

        $this->actingAs($lain)
            ->get('/pesanan-saya/'.$pesanan->id)
            ->assertForbidden();
    }

    public function test_customer_dapat_membatalkan_pesanan_dan_stok_kembali(): void
    {
        $customer = $this->buatCustomer();
        $jas = $this->buatJas(['stok' => 3]);
        $pesanan = $this->buatPesanan($customer, $jas, ['jumlah' => 2]);

        $this->actingAs($customer)
            ->post('/pesanan-saya/'.$pesanan->id.'/batal')
            ->assertRedirect();

        $this->assertSame(StatusPesanan::Dibatalkan, $pesanan->fresh()->status);
        $this->assertSame(5, $jas->fresh()->stok);
    }

    public function test_testimoni_hanya_bisa_dibuat_setelah_pesanan_selesai(): void
    {
        $customer = $this->buatCustomer();
        $jas = $this->buatJas();

        // Belum ada pesanan selesai -> gagal validasi.
        $this->actingAs($customer)->post('/testimoni', [
            'jas_id' => $jas->id,
            'rating' => 5,
            'komentar' => 'Pelayanan sangat memuaskan sekali.',
        ])->assertSessionHasErrors('jas_id');

        $this->buatPesanan($customer, $jas, ['status' => StatusPesanan::Selesai]);

        $this->actingAs($customer)->post('/testimoni', [
            'jas_id' => $jas->id,
            'rating' => 5,
            'komentar' => 'Pelayanan sangat memuaskan sekali.',
        ])->assertRedirect();

        $this->assertDatabaseHas('testimoni', [
            'user_id' => $customer->id,
            'jas_id' => $jas->id,
            'rating' => 5,
        ]);
    }
}
