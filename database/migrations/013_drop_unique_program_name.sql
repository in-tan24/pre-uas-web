-- Allow duplicate program_name (as requested)
ALTER TABLE `programs`
  DROP INDEX `uq_programs_name`;

