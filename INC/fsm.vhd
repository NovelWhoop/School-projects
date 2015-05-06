-- fsm.vhd: Finite State Machine
-- Author(s): Roman Halik
--
library ieee;
use ieee.std_logic_1164.all;
-- ----------------------------------------------------------------------------
--                        Entity declaration
-- ----------------------------------------------------------------------------
entity fsm is
port(
   CLK         : in  std_logic;
   RESET       : in  std_logic;

   -- Input signals
   KEY         : in  std_logic_vector(15 downto 0);
   CNT_OF      : in  std_logic;

   -- Output signals
   FSM_CNT_CE  : out std_logic;
   FSM_MX_MEM  : out std_logic;
   FSM_MX_LCD  : out std_logic;
   FSM_LCD_WR  : out std_logic;
   FSM_LCD_CLR : out std_logic
);
end entity fsm;

-- ----------------------------------------------------------------------------
--                      Architecture declaration
-- ----------------------------------------------------------------------------
architecture behavioral of fsm is
   type t_state is (INPUT_1, INPUT_2,
                    INPUT_3_A, INPUT_4_A, INPUT_5_A, INPUT_6_A, INPUT_7_A, INPUT_8_A, INPUT_9_A, INPUT_10_A, INPUT_DONE_A,
                    INPUT_3_B, INPUT_4_B, INPUT_5_B, INPUT_6_B, INPUT_7_B, INPUT_8_B, INPUT_9_B, INPUT_10_B, INPUT_DONE_B,
                    INPUT_ERROR, PRINT_ERROR, PRINT_OK, FINISH);
   signal present_state, next_state : t_state;

begin
-- -------------------------------------------------------
sync_logic : process(RESET, CLK)
begin
   if (RESET = '1') then
      present_state <= INPUT_1;
   elsif (CLK'event AND CLK = '1') then
      present_state <= next_state;
   end if;
end process sync_logic;

-- -------------------------------------------------------
next_state_logic : process(present_state, KEY, CNT_OF)
begin
   case (present_state) is
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_1 =>
      next_state <= INPUT_1;
      if (KEY(1) = '1') then
          next_state <= INPUT_2;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
    when INPUT_2 =>
      next_state <= INPUT_2;
      if (KEY(0) = '1') then
          next_state <= INPUT_3_A;
      elsif (KEY(5) = '1') then
          next_state <= INPUT_3_B;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_3_A =>
      next_state <= INPUT_3_A;
      if (KEY(0) = '1') then
          next_state <= INPUT_4_A;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_4_A =>
      next_state <=  INPUT_4_A;
      if (KEY(5) = '1') then
          next_state <=  INPUT_5_A;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_5_A =>
      next_state <=  INPUT_5_A;
      if (KEY(5) = '1') then
          next_state <=  INPUT_6_A;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then 
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when  INPUT_6_A =>
      next_state <=  INPUT_6_A;
      if (KEY(8) = '1') then
          next_state <=  INPUT_7_A;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when  INPUT_7_A =>
      next_state <=  INPUT_7_A;
      if (KEY(5) = '1') then
          next_state <=  INPUT_8_A;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_8_A =>
      next_state <= INPUT_8_A;
      if (KEY(1) = '1') then
          next_state <= INPUT_9_A;
      elsif (KEY(7) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then 
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_9_A =>
      next_state <= INPUT_9_A;
      if (KEY(0) = '1') then
          next_state <= INPUT_10_A;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_10_A =>
      next_state <= INPUT_10_A;
      if (KEY(6) = '1') then
          next_state <= INPUT_DONE_A;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_DONE_A =>
      next_state <= INPUT_DONE_A;
      if (KEY(15) = '1') then
         next_state <= PRINT_OK;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;

-- -------------------------------------------------------
   when INPUT_3_B =>
      next_state <= INPUT_3_B;
      if (KEY(6) = '1') then
          next_state <= INPUT_4_B;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_4_B =>
      next_state <=  INPUT_4_B;
      if (KEY(4) = '1') then
          next_state <=  INPUT_5_B;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_5_B =>
      next_state <=  INPUT_5_B;
      if (KEY(2) = '1') then
          next_state <=  INPUT_6_B;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when  INPUT_6_B =>
      next_state <=  INPUT_6_B;
      if (KEY(4) = '1') then
          next_state <=  INPUT_7_B;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when  INPUT_7_B =>
      next_state <=  INPUT_7_B;
      if (KEY(3) = '1') then
          next_state <=  INPUT_8_B;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_8_B =>
      next_state <= INPUT_8_B;
      if (KEY(4) = '1') then
          next_state <= INPUT_9_B;
      elsif (KEY(7) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_9_B =>
      next_state <= INPUT_9_B;
      if (KEY(9) = '1') then
          next_state <= INPUT_10_B;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_10_B =>
      next_state <= INPUT_10_B;
      if (KEY(8) = '1') then
          next_state <= INPUT_DONE_B;
      elsif (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when INPUT_DONE_B =>
      next_state <= INPUT_DONE_B;
      if (KEY(15) = '1') then
         next_state <= PRINT_OK;
      elsif (KEY(14 downto 0) /= "000000000000000") then
         next_state <= INPUT_ERROR;
      end if;

-- -------------------------------------------------------
   when INPUT_ERROR =>
      next_state <= INPUT_ERROR;
      if (KEY(15) = '1') then
         next_state <= PRINT_ERROR;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when PRINT_ERROR =>
      next_state <= PRINT_ERROR;
      if (CNT_OF = '1') then
         next_state <= FINISH;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when PRINT_OK =>
      next_state <= PRINT_OK;
      if (CNT_OF = '1') then
         next_state <= FINISH;
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when FINISH =>
      next_state <= FINISH;
      if (KEY(15) = '1') then
         next_state <= INPUT_1;
      end if;
   end case;
end process next_state_logic;

-- -------------------------------------------------------
output_logic : process(present_state, KEY)
begin
   FSM_CNT_CE     <= '0';
   FSM_MX_MEM     <= '0';
   FSM_MX_LCD     <= '0';
   FSM_LCD_WR     <= '0';
   FSM_LCD_CLR    <= '0';

   case (present_state) is
   -- - - - - - - - - - - - - - - - - - - - - - -
   when PRINT_ERROR =>
      FSM_CNT_CE     <= '1';
      FSM_MX_LCD     <= '1';
      FSM_LCD_WR     <= '1';
   -- - - - - - - - - - - - - - - - - - - - - - -
   when PRINT_OK =>
      FSM_MX_MEM     <= '1';
      FSM_CNT_CE     <= '1';
      FSM_MX_LCD     <= '1';
      FSM_LCD_WR     <= '1';

   -- - - - - - - - - - - - - - - - - - - - - - -
   when FINISH =>
      if (KEY(15) = '1') then
         FSM_LCD_CLR    <= '1';
      end if;
   -- - - - - - - - - - - - - - - - - - - - - - -
   when others =>
      if (KEY(14 downto 0) /= "000000000000000") then
         FSM_LCD_WR     <= '1';
      end if;
      if (KEY(15) = '1') then
         FSM_LCD_CLR    <= '1';
      end if;

   end case;
end process output_logic;

end architecture behavioral;
