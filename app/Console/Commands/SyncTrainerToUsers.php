<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Trainer;
use App\Models\User;

class SyncTrainerToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:trainer-to-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ dữ liệu từ trainer_info sang users (name, position, email, phone)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Bắt đầu đồng bộ dữ liệu từ trainer_info sang users...');
        
        // Get all trainers with user_id
        $trainers = Trainer::whereNotNull('user_id')->get();
        
        if ($trainers->isEmpty()) {
            $this->warn('Không tìm thấy trainer nào có user_id.');
            return Command::SUCCESS;
        }
        
        $this->info("Tìm thấy {$trainers->count()} trainer(s) có user_id.");
        $this->newLine();
        
        $updatedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        
        $progressBar = $this->output->createProgressBar($trainers->count());
        $progressBar->start();
        
        foreach ($trainers as $trainer) {
            try {
                $user = User::find($trainer->user_id);
                
                if (empty($user)) {
                    $this->newLine();
                    $this->warn("  Trainer ID {$trainer->id}: Không tìm thấy user với ID {$trainer->user_id}");
                    $skippedCount++;
                    $progressBar->advance();
                    continue;
                }
                
                $hasChanges = false;
                $changes = [];
                
                // Sync name
                if (!empty($trainer->name) && $user->name !== $trainer->name) {
                    $oldName = $user->name ?? '(null)';
                    $user->name = $trainer->name;
                    $hasChanges = true;
                    $changes[] = "name: '{$oldName}' -> '{$trainer->name}'";
                }
                
                // Sync position
                if ($user->position !== $trainer->position) {
                    $oldPosition = $user->position ?? '(null)';
                    $newPosition = $trainer->position ?? '(null)';
                    $user->position = $trainer->position;
                    $hasChanges = true;
                    $changes[] = "position: '{$oldPosition}' -> '{$newPosition}'";
                }
                
                // Sync phone
                if ($user->phone !== $trainer->phone) {
                    $oldPhone = $user->phone ?? '(null)';
                    $newPhone = $trainer->phone ?? '(null)';
                    $user->phone = $trainer->phone;
                    $hasChanges = true;
                    $changes[] = "phone: '{$oldPhone}' -> '{$newPhone}'";
                }
                
                // Sync email
                if (!empty($trainer->email) && $user->email !== $trainer->email) {
                    // Check if email already exists for another user
                    $existingUser = User::where('email', $trainer->email)
                        ->where('id', '!=', $user->id)
                        ->first();
                    
                    if ($existingUser) {
                        $this->newLine();
                        $this->warn("  Trainer ID {$trainer->id} (User ID {$user->id}): Email '{$trainer->email}' đã tồn tại cho user ID {$existingUser->id}. Bỏ qua.");
                        $skippedCount++;
                        $progressBar->advance();
                        continue;
                    }
                    
                    $oldEmail = $user->email ?? '(null)';
                    $user->email = $trainer->email;
                    $hasChanges = true;
                    $changes[] = "email: '{$oldEmail}' -> '{$trainer->email}'";
                }
                
                // Update user if there are changes
                if ($hasChanges) {
                    $user->save();
                    $updatedCount++;
                    
                    if ($this->option('verbose')) {
                        $this->newLine();
                        $this->line("  Trainer ID {$trainer->id} (User ID {$user->id}):");
                        foreach ($changes as $change) {
                            $this->line("    - {$change}");
                        }
                    }
                } else {
                    $skippedCount++;
                }
                
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  Trainer ID {$trainer->id}: Lỗi - {$e->getMessage()}");
                $errorCount++;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Summary
        $this->info('Kết quả đồng bộ:');
        $this->line("  ✓ Đã cập nhật: {$updatedCount} user(s)");
        $this->line("  ⊘ Đã bỏ qua: {$skippedCount} trainer(s) (không có thay đổi hoặc lỗi)");
        if ($errorCount > 0) {
            $this->error("  ✗ Lỗi: {$errorCount} trainer(s)");
        }
        
        $this->newLine();
        $this->info('Hoàn thành đồng bộ dữ liệu!');
        
        return Command::SUCCESS;
    }
}

