% projekt do predmetu ISS - operace nad cernobilym obrazkem
% autor: Roman Halik, xhalik01

% nacteni obrazku
  I = imread('../xhalik01.bmp');

% zaostreni pomoci linearniho filtru
  filter1 = [-0.5 -0.5 -0.5; -0.5 5.0 -0.5; -0.5 -0.5 -0.5];
  I1 = imfilter(I, filter1);
  imwrite(I1, '../step1.bmp');

% preklopeni kolem svisle osy
  I2 = fliplr(I1);
  imwrite(I2, '../step2.bmp');

% medianovy filtr
  I3 = medfilt2(I2, [5 5]);
  imwrite(I3, '../step3.bmp');

% rozmazani
  filter2 = ([1 1 1 1 1; 1 3 3 3 1; 1 3 9 3 1; 1 3 3 3 1; 1 1 1 1 1] / 49);
  I4 = imfilter(I3, filter2);
  imwrite(I4, '../step4.bmp');

% chyba v obraze
  tmpI = double(fliplr(I4)); % otocime zpet I4, aby byl stejne natocen, jako original
  I = double(I); % a prevedeme originalni obrazek na double
  chyba = 0; % vynulovani 'pocitadla' chyby
  for (x = 1:512)
    for (y = 1:512)
      chyba = chyba + double(abs(I(x,y) - tmpI(x,y)));
    end;
  end;
  chyba = chyba/512/512

% roztazeni histogramu
  min = min(min(im2double(I4)));
  max = max(max(im2double(I4)));
  I5 = imadjust(I4, [min max], [0 1]);
  I5 = uint8(I5);
  imwrite(I5, '../step5.bmp');

% stredni hodnoty a odchylky
  mean_no_hist = mean2(double(I4)) % pred roztazenim histogramu
  std_no_hist = std2(double(I4))
  mean_hist = mean2(double(I5)) % po roztazeni histogramu
  std_hist = std2(double(I5))

% kvantizace obrazku na 2 bity
  a = 0 ;
  b = 255;
  N = 2;
  I5 = double(I5);
  I6 = zeros(512,512);
  for m = 1:512
    for n = 1:512
      I6(m,n) = round(((2^N) - 1) * (I5(m,n) - a) / (b - a)) * (b - a) / ((2^N) - 1) + a;
    end;
  end;
  I6 = uint8(I6);
  imwrite(I6, '../step6.bmp');
  