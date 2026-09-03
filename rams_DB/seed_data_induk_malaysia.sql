-- Data induk Malaysia untuk penggunaan sebenar.
-- Skrip ini idempotent: data sedia ada dikekalkan dan pendua tidak ditambah.

START TRANSACTION;

-- Pembetulan dan terjemahan data sedia ada (ID dikekalkan).
UPDATE states SET state_name = 'SELANGOR' WHERE state_name = 'SELAMGOR';
UPDATE managed_by_add_data SET name = 'Pengurusan' WHERE name = 'Management';
UPDATE store_location SET name = 'Stor Utama' WHERE name = 'Store';

-- Negeri dan Wilayah Persekutuan Malaysia.
INSERT INTO states (state_name, colour, active)
SELECT seed.state_name, seed.colour, 1
FROM (
    SELECT 'JOHOR' state_name, '#1F77B4' colour UNION ALL
    SELECT 'KEDAH', '#FF7F0E' UNION ALL
    SELECT 'KELANTAN', '#2CA02C' UNION ALL
    SELECT 'MELAKA', '#D62728' UNION ALL
    SELECT 'NEGERI SEMBILAN', '#9467BD' UNION ALL
    SELECT 'PAHANG', '#8C564B' UNION ALL
    SELECT 'PULAU PINANG', '#E377C2' UNION ALL
    SELECT 'PERAK', '#7F7F7F' UNION ALL
    SELECT 'PERLIS', '#BCBD22' UNION ALL
    SELECT 'SABAH', '#17BECF' UNION ALL
    SELECT 'SARAWAK', '#2E86AB' UNION ALL
    SELECT 'SELANGOR', '#A23B72' UNION ALL
    SELECT 'TERENGGANU', '#F18F01' UNION ALL
    SELECT 'WILAYAH PERSEKUTUAN KUALA LUMPUR', '#C73E1D' UNION ALL
    SELECT 'WILAYAH PERSEKUTUAN LABUAN', '#3B1F2B' UNION ALL
    SELECT 'WILAYAH PERSEKUTUAN PUTRAJAYA', '#4F6D7A'
) seed
WHERE NOT EXISTS (
    SELECT 1 FROM states s WHERE UPPER(TRIM(s.state_name)) = seed.state_name
);

-- Lokasi pentadbiran utama yang sebenar bagi setiap negeri/wilayah.
INSERT INTO locations (name, state_id, address, lat, `long`, colour, active)
SELECT seed.location_name, s.id, seed.address, seed.lat, seed.longitude, s.colour, 1
FROM (
    SELECT 'JOHOR' state_name, 'Johor Bahru' location_name, 'Johor Bahru, Johor' address, '1.4927' lat, '103.7414' longitude UNION ALL
    SELECT 'KEDAH', 'Alor Setar', 'Alor Setar, Kedah', '6.1248', '100.3678' UNION ALL
    SELECT 'KELANTAN', 'Kota Bharu', 'Kota Bharu, Kelantan', '6.1254', '102.2381' UNION ALL
    SELECT 'MELAKA', 'Bandar Melaka', 'Bandar Melaka, Melaka', '2.1896', '102.2501' UNION ALL
    SELECT 'NEGERI SEMBILAN', 'Seremban', 'Seremban, Negeri Sembilan', '2.7297', '101.9381' UNION ALL
    SELECT 'PAHANG', 'Kuantan', 'Kuantan, Pahang', '3.8077', '103.3260' UNION ALL
    SELECT 'PULAU PINANG', 'George Town', 'George Town, Pulau Pinang', '5.4141', '100.3288' UNION ALL
    SELECT 'PERAK', 'Ipoh', 'Ipoh, Perak', '4.5975', '101.0901' UNION ALL
    SELECT 'PERLIS', 'Kangar', 'Kangar, Perlis', '6.4414', '100.1986' UNION ALL
    SELECT 'SABAH', 'Kota Kinabalu', 'Kota Kinabalu, Sabah', '5.9804', '116.0735' UNION ALL
    SELECT 'SARAWAK', 'Kuching', 'Kuching, Sarawak', '1.5533', '110.3592' UNION ALL
    SELECT 'SELANGOR', 'Shah Alam', 'Shah Alam, Selangor', '3.0738', '101.5183' UNION ALL
    SELECT 'TERENGGANU', 'Kuala Terengganu', 'Kuala Terengganu, Terengganu', '5.3296', '103.1370' UNION ALL
    SELECT 'WILAYAH PERSEKUTUAN KUALA LUMPUR', 'Kuala Lumpur', 'Kuala Lumpur', '3.1390', '101.6869' UNION ALL
    SELECT 'WILAYAH PERSEKUTUAN LABUAN', 'Bandar Labuan', 'Bandar Labuan, Wilayah Persekutuan Labuan', '5.2767', '115.2417' UNION ALL
    SELECT 'WILAYAH PERSEKUTUAN PUTRAJAYA', 'Putrajaya', 'Putrajaya', '2.9264', '101.6964'
) seed
JOIN states s ON UPPER(TRIM(s.state_name)) = seed.state_name
WHERE NOT EXISTS (
    SELECT 1 FROM locations l
    WHERE UPPER(TRIM(l.name)) = UPPER(seed.location_name)
      AND l.state_id = s.id
);

-- Unit pengurusan dalam Bahasa Melayu; singkatan organisasi sedia ada dikekalkan.
INSERT INTO managed_by_add_data (name, active)
SELECT seed.name, 1
FROM (
    SELECT 'Jabatan Teknologi Maklumat' name UNION ALL
    SELECT 'Jabatan Operasi' UNION ALL
    SELECT 'Jabatan Penyelenggaraan' UNION ALL
    SELECT 'Jabatan Pentadbiran' UNION ALL
    SELECT 'Jabatan Kewangan' UNION ALL
    SELECT 'Pengurusan Fasiliti'
) seed
WHERE NOT EXISTS (
    SELECT 1 FROM managed_by_add_data m WHERE UPPER(TRIM(m.name)) = UPPER(seed.name)
);

-- Lokasi stor operasi dalam Bahasa Melayu.
INSERT INTO store_location (name, active)
SELECT seed.name, 1
FROM (
    SELECT 'Stor Teknologi Maklumat' name UNION ALL
    SELECT 'Stor Alat Ganti' UNION ALL
    SELECT 'Stor Penyelenggaraan' UNION ALL
    SELECT 'Stor Operasi' UNION ALL
    SELECT 'Stor Pelupusan'
) seed
WHERE NOT EXISTS (
    SELECT 1 FROM store_location sl WHERE UPPER(TRIM(sl.name)) = UPPER(seed.name)
);

COMMIT;
