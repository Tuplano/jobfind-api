<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\ExperienceLevel;
use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\EmployeeProfile;
use App\Models\EmployerProfile;
use App\Models\Job;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $employerCount = 55; // previous 5 + 50 more
        $employeeCount = 68; // previous 18 + 50 more

        $skills = collect([
            'PHP',
            'Laravel',
            'MySQL',
            'REST API',
            'JavaScript',
            'TypeScript',
            'React',
            'Vue.js',
            'Node.js',
            'Git',
            'Docker',
            'AWS',
            'Redis',
            'Tailwind CSS',
            'Testing',
        ])->map(fn (string $name) => Skill::query()->firstOrCreate(['name' => $name]));

        User::query()->create([
            'first_name' => 'System',
            'last_name' => 'Admin',
            'email' => 'admin@jobfind.local',
            'password' => 'password',
            'role' => UserRole::Admin,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $employers = User::factory()
            ->count($employerCount)
            ->state(fn () => [
                'role' => UserRole::Employer,
                'is_active' => true,
                'email_verified_at' => now(),
            ])
            ->create();

        $industries = ['Software', 'FinTech', 'E-commerce', 'Healthcare', 'Education'];
        $companySizes = ['1-10', '11-50', '51-200', '201-500', '500+'];
        $cities = ['Manila', 'Cebu', 'Davao', 'Makati', 'Taguig'];

        foreach ($employers as $index => $employer) {
            EmployerProfile::query()->create([
                'user_id' => $employer->id,
                'company_name' => fake()->company(),
                'industry' => $industries[$index % count($industries)],
                'company_size' => Arr::random($companySizes),
                'founded_year' => fake()->numberBetween(1995, 2022),
                'description' => fake()->paragraph(3),
                'website' => 'https://'.fake()->domainName(),
                'contact_email' => $employer->email,
                'contact_phone' => fake()->numerify('9#########'),
                'dial_code' => '+63',
                'address' => fake()->streetAddress(),
                'city' => Arr::random($cities),
                'country' => 'Philippines',
                'setup_completed' => true,
            ]);
        }

        $employees = User::factory()
            ->count($employeeCount)
            ->state(fn () => [
                'role' => UserRole::Employee,
                'is_active' => true,
                'email_verified_at' => now(),
            ])
            ->create();

        foreach ($employees as $employee) {
            $profile = EmployeeProfile::query()->create([
                'user_id' => $employee->id,
                'phone' => fake()->numerify('9#########'),
                'dial_code' => '+63',
                'city' => Arr::random($cities),
                'country' => 'Philippines',
                'date_of_birth' => fake()->dateTimeBetween('-40 years', '-19 years')->format('Y-m-d'),
                'linkedin_url' => 'https://www.linkedin.com/in/'.fake()->userName(),
                'portfolio_url' => fake()->boolean(60) ? 'https://'.fake()->domainName() : null,
                'about' => fake()->paragraph(2),
                'resume_path' => 'resumes/'.fake()->uuid().'.pdf',
                'resume_original_name' => 'resume-'.strtolower($employee->first_name).'.pdf',
                'is_open_to_work' => fake()->boolean(85),
                'setup_completed' => true,
            ]);

            $profile->skills()->sync(
                $skills->random(fake()->numberBetween(3, 7))->pluck('id')->all()
            );
        }

        $jobTitles = [
            'Backend Developer',
            'Frontend Engineer',
            'Full Stack Developer',
            'QA Engineer',
            'DevOps Engineer',
            'Product Designer',
            'Technical Support Engineer',
            'Data Analyst',
        ];

        $jobs = collect();
        foreach ($employers as $employer) {
            $jobCount = fake()->numberBetween(3, 6);
            for ($i = 0; $i < $jobCount; $i++) {
                $job = Job::query()->create([
                    'employer_id' => $employer->id,
                    'title' => Arr::random($jobTitles),
                    'description' => fake()->paragraphs(3, true),
                    'location' => Arr::random($cities).', Philippines',
                    'is_remote' => fake()->boolean(45),
                    'type' => Arr::random(JobType::cases()),
                    'experience_level' => Arr::random(ExperienceLevel::cases()),
                    'salary_min' => fake()->numberBetween(30000, 90000),
                    'salary_max' => fake()->numberBetween(95000, 200000),
                    'salary_currency' => 'PHP',
                    'status' => fake()->randomElement([
                        JobStatus::Active,
                        JobStatus::Active,
                        JobStatus::Active,
                        JobStatus::Draft,
                        JobStatus::Paused,
                    ]),
                    'expires_at' => now()->addDays(fake()->numberBetween(15, 90)),
                ]);

                $job->skills()->sync(
                    $skills->random(fake()->numberBetween(2, 5))->pluck('id')->all()
                );

                $jobs->push($job);
            }
        }

        $activeJobs = $jobs->where('status', JobStatus::Active)->values();
        foreach ($employees as $employee) {
            $appliedJobIds = [];
            $applyCount = min($activeJobs->count(), fake()->numberBetween(8, 15));

            for ($i = 0; $i < $applyCount; $i++) {
                $job = $activeJobs->whereNotIn('id', $appliedJobIds)->random();
                $status = Arr::random(ApplicationStatus::cases());

                Application::query()->create([
                    'job_listing_id' => $job->id,
                    'employee_id' => $employee->id,
                    'status' => $status,
                    'cover_letter' => fake()->paragraph(2),
                    'resume_path' => 'applications/'.fake()->uuid().'.pdf',
                    'resume_original_name' => 'application-'.strtolower($employee->first_name).'.pdf',
                    'employer_notes' => $status === ApplicationStatus::Pending ? null : fake()->sentence(),
                    'reviewed_at' => $status === ApplicationStatus::Pending ? null : now()->subDays(fake()->numberBetween(1, 20)),
                ]);

                $appliedJobIds[] = $job->id;
            }
        }
    }
}
