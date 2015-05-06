-- Author: Roman Halik, xhalik01
-- 11/2014

library IEEE;
use IEEE.std_logic_1164.all;
use IEEE.std_logic_unsigned.all;
use IEEE.std_logic_arith.all;

-- rozhrani led displaye
entity ledc8x8 is
  port
  (
    SMCLK: in std_logic;
    RESET: in std_logic;
    ROW: out std_logic_vector(0 to 7);
    LED: out std_logic_vector(0 to 7)
  );
  end ledc8x8;

-- vnitrni signaly
architecture behavioral of ledc8x8 is
  signal rowSignal: std_logic_vector(7 downto 0);
  signal frequency: std_logic_vector(7 downto 0);
  signal mysmclk: std_logic;

-- delic kmitoctu
begin
  process(SMCLK, RESET)
  begin
    if RESET = '1' then
      frequency <= (others => '0');
    elsif rising_edge(SMCLK) then
      frequency <= frequency + 1;
    end if;
    mysmclk <= frequency(7);
  end process;

-- kruhovy registr
  process(mysmclk, RESET)
  begin
    if RESET = '1' then
      rowSignal <= "10000000";
    elsif rising_edge(mysmclk) then
      rowSignal <= rowSignal(0) & rowSignal(7 downto 1);
    end if;
  end process;

-- stavy diod
  process(rowSignal)
  begin
    case rowSignal is
      when "10000000" =>  LED <= "01011000";
      when "01000000" =>  LED <= "01010110";
      when "00100000" =>  LED <= "01010110";
      when "00010000" =>  LED <= "01010110";
      when "00001000" =>  LED <= "00011000";
      when "00000100" =>  LED <= "01010110";
      when "00000010" =>  LED <= "01010110";
      when "00000001" =>  LED <= "01010110";
      when others =>  LED <= "00000000";
    end case;
  end process;

  ROW <= rowSignal;

end behavioral;
