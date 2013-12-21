<?php
######################## PAGES ########################
#ERROR 
define('LANG_ERROR', 'Hata oluştu. Detay: ');

#PAGE
define('LANG_PAGE_TITLE', 'Timety | Asla Kaçırma');

#MAIL
define('LANG_MAIL_CONFIRM_ACCOUNT_EMAIL', 'Lütfen E-Postanızı onaylayın.');

#INDEX
define('LANG_PAGE_INDEX_REGISTRATION_COMPLETE', 'Üyeliğiniz tamamlandı.');
define('LANG_PAGE_INDEX_UNREGISTER_WEEKLY_MAIL', 'Abonelikten Ayrıldınız');
define('LANG_PAGE_INDEX_REGISTRATION_USER_DOESNT_EXIST', 'Kullanıcı yok');
define('LANG_PAGE_INDEX_REGISTRATION_PARAMETERS_WRONG', 'Parametreler hatalı');
define('LANG_PAGE_INDEX_PAGE_CLICK_HERE_ADD_EVENT', 'Etkinlik eklemek için tıklayın');

#INDEX - MY TIMETY
define('LANG_PAGE_INDEX_MY_TIMETY_TODAY', 'Bugün');

#INDEX - ADD EVENT
define('LANG_PAGE_INDEX_ADD_ERR_TITLE_EMPTY', 'Etkinlik başlığı boş olamaz');
define('LANG_PAGE_INDEX_ADD_ERR_LOC_EMPTY', 'Etkinlik lokasyonu boş olamaz');
define('LANG_PAGE_INDEX_ADD_ERR_DESC_EMPTY', 'Etkinlik açıklaması boş olamaz');
define('LANG_PAGE_INDEX_ADD_ERR_UPLOAD', 'Resim yüklemelisiniz');
define('LANG_PAGE_INDEX_ADD_ERR_START_DATE_NOT_VALID', 'Etkinlik başlangıç saati geçersiz');
define('LANG_PAGE_INDEX_ADD_ERR_START_TIME_NOT_VALID', 'Etkinlik bitiş saati geçersiz');
define('LANG_PAGE_INDEX_ADD_ERR_END_TIME_NOT_VALID', 'Bitiş saati geçersiz');
define('LANG_PAGE_INDEX_ADD_ERR_END_DATE_NOT_VALID', 'Bitiş tarihi geçersiz');
define('LANG_PAGE_INDEX_ADD_SUC_CREATED', 'Etkinlik başarıyla oluşturuldu.');
define('LANG_PAGE_INDEX_ADD_PRI_PUBLIC','Genel');
define('LANG_PAGE_INDEX_ADD_PRI_PRIVATE','Özel');

#INDEX - ADD EVENT - UPLOAD IMAGE  {} degistirme
define('LANG_PAGE_INDEX_ADD_UPL_TYPE_ERROR', '{file} dosya uzantısı geçersiz. Sadece {extensions} geçerlidir.');
define('LANG_PAGE_INDEX_ADD_UPL_SIZE_ERROR', '{file} dosya çok büyük, maksimum dosya boyutu {sizeLimit}.');
define('LANG_PAGE_INDEX_ADD_UPL_MIN_SIZE_ERROR', '{file} dosya çok küçük, minimum dosya boyutu {minSizeLimit}.');
define('LANG_PAGE_INDEX_ADD_UPL_EMPTY_ERROR', '{file} dosya boş, dosyayı seçmeden tekrardan yükleyin.');
define('LANG_PAGE_INDEX_ADD_UPL_LEAVE_ERROR', 'Dosyalarınız yükleniyor, şuan çıkarsanız işlem iptal olur.');

#PROFILE BACTH
define('LANG_PROFILE_BACTH_FOLLOWING', 'Takip Edilen');
define('LANG_PROFILE_BACTH_FOLLOWERS', 'Takipçi');
define('LANG_PROFILE_BACTH_LIKES', 'Beğeniler');
define('LANG_PROFILE_BACTH_RESHARE', 'Paylaşılanlar');
define('LANG_PROFILE_BACTH_JOINED', 'Katılımlar');
define('LANG_PROFILE_BACTH_MAYBE', 'Belki');
define('LANG_PROFILE_BACTH_IGNORED', 'Önemseme');
define('LANG_PROFILE_BACTH_CRATED_EVENTS', 'Oluşturduklarım');

#PAGE TOP
define('LANG_PAGE_TOP_NO_USER_HEADER_TEXT', 'Şimdi üye olun ve etrafınızdaki tüm etkinlikleri Keşfedin, Paylaşın ve Takip edin'); 
define('LANG_PAGE_TOP_CITY_INPUT_HINT', 'Lokasyon');
define('LANG_PAGE_TOP_CATEGORY_RECOMMENDED', 'Önerilen');
define('LANG_PAGE_TOP_CATEGORY_EVERYTHIG', 'Tümü');
define('LANG_PAGE_TOP_SEARCH_INPUT_HINT', 'Etkinlik ara');
define('LANG_PAGE_TOP_ADD_EVENT', 'Etkinlik ekle');
define('LANG_PAGE_TOP_MENU_MY_TIMETY', 'Timety\'m');
define('LANG_PAGE_TOP_MENU_FOLLOWING', 'Takip Edilen');
define('LANG_PAGE_TOP_MENU_ADD_INTEREST', 'Beğeni ekleyin');
define('LANG_PAGE_TOP_MENU_SETTINGS', 'Ayarlar');
define('LANG_PAGE_TOP_MENU_LOGOUT', 'Çıkış');
define('LANG_PAGE_TOP_NO_USER_CREATE_ACCOUNT', 'hesap yaratın');
define('LANG_PAGE_TOP_NO_USER_SIGNIN', 'giriş yapın');
define('LANG_PAGE_TOP_MEDIA', 'Medya');

#INDEX - MY TIMETY MENU
define('LANG_PAGE_MY_TIMETY_MENU_RECOMMENDED_EVENTS', 'Önerilen Etkinlikler');
define('LANG_PAGE_MY_TIMETY_MENU_ALL_EVENTS', 'Tüm Etkinlikler');
define('LANG_PAGE_MY_TIMETY_MENU_CATEGORIES', 'Kategoriler');
define('LANG_PAGE_MY_TIMETY_MENU_WEEKEND', 'Hafta Sonu');
define('LANG_PAGE_MY_TIMETY_MENU_FOURYOU', 'Sana Özel');
define('LANG_PAGE_MY_TIMETY_WEEKEND_MENU_ALL_EVENTS', 'Tüm Etkinlikler');
define('LANG_PAGE_MY_TIMETY_WEEKEND_MENU_TODAY', 'Bugün');
define('LANG_PAGE_MY_TIMETY_WEEKEND_MENU_TOMORROW', 'Yarın');
define('LANG_PAGE_MY_TIMETY_WEEKEND_MENU_THISWEEKEND', 'Bu Hafta Sonu');
define('LANG_PAGE_MY_TIMETY_WEEKEND_MENU_NEXT_7_DAYS', 'Önümüzdeki 7 Gün');
define('LANG_PAGE_MY_TIMETY_WEEKEND_MENU_NEXT_30_DAYS', 'Önümüzdeki 30 Gün');

#PAGE REGISTER TOP NO USER REGISTER
define('LANG_PAGE_REGISTER_TOP_NO_USER_HEADER_TEXT', 'Etrafınızdaki tüm etkinlikleri Keşfedin, Paylaşın ve Takip edin');
define('LANG_PAGE_REGISTER_TOP_NO_USER_CREATE_ACCOUNT', 'üye olun');
define('LANG_PAGE_REGISTER_TOP_NO_USER_SIGNIN', 'giriş yapın');
define('LANG_PAGE_REGISTER_LOGO_TEXT', 'Şimdi kayıt ol </br> ve Asla Kaçırma');

#INDEX - ADD EVENT- TEMPLETE
define('LANG_PAGE_INDEX_ADD_TEMPLATE_CLICK_HERE_TO_ADD_IMG', 'resim eklemek için tıklayın');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_TITLE_PLACEHOLDER', 'Etkinlik başlığı');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_SOCIAL_LABEL_EXPOT', 'Burayada ekle');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_SOCIAL_LABEL_FACEBOOK', 'Facebook Etkinliklerine Ekle');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_SOCIAL_LABEL_GOOGLE', 'Google Tkvimine Ekle');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_SOCIAL_LABEL_OUTLOOK', 'ICS Olarak indir');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_LOCATION_PLACEHOLDER', 'lokasyon');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_LINK_ADD', 'Ekle');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_LINK_CLOSE', 'Kapat');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_TAG_PLACEHOLDER', 'etiket');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_DESC_PLACEHOLDER', 'açıklama');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_PEOPLE_PLACEHOLDER', 'davet edin');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_BUTTON_CANCEL', 'İptal');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_BUTTON_DELETE', 'Etkinliği Sil');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_BUTTON_ADD_EVENT', 'Etkinliği Oluştur');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_DATE_TO', 'to');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_ADD_IMG', 'resim ekle');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_ADD_VIDEO', 'veya YouTube URL');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_START_DATE', 'Başlangıç tarihi');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_START_TIME', 'Başlangıç zamanı');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_END_DATE', 'Bitiş tarihi');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_END_TIME', 'Bitiş zamanı');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_ADD_END_DATE_TIME', '+ Bitiş tarihi ekle');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_MORE_DETAIL', 'Daha Fazla Detay');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_ADD_PRICE', 'Fiyat Ekle');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_PRICE_UNIT_TL', 'TL');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_PRICE_UNIT_USD', 'USD');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_PRICE_UNIT_EURO', 'EURO');
define('LANG_PAGE_INDEX_ADD_TEMPLATE_LINK_TO_OFFICIAL_WEB_PAGE', 'Resmi siteye link');

#INDEX - EVENT DETAIL 
define('LANG_PAGE_EVENT_DETAIL_FOLLOW', 'takip et');
define('LANG_PAGE_EVENT_DETAIL_UNFOLLOW', 'takibi bırak');
define('LANG_PAGE_EVENT_DETAIL_FOLLOWING', 'takip ediliyor');
define('LANG_PAGE_EVENT_DETAIL_JOINED_TEXT', 'Katılanlar');
define('LANG_PAGE_EVENT_DETAIL_COMMENT_INPUT_PLACEHOLDER', 'Yorum yazın...');
define('LANG_PAGE_EVENT_DETAIL_COMMENT_BUTTON', 'Gönder');
define('LANG_PAGE_EVENT_DETAIL_COMMENT_SEE_MORE', 'Tüm yorumları görün...');

#PAGE TEMPLATE - FOLLOWING 
define('LANG_PAGE_FOLLOWING_TEMPLATE_FIND_FRIENDS', 'Arkadaşlarınızı bulun');
define('LANG_PAGE_FOLLOWING_TEMPLATE_PEOPLE_YOU_KNOW', 'Tanıdığınız insanlar');
define('LANG_PAGE_FOLLOWING_TEMPLATE_FOLLOW', 'takip et');
define('LANG_PAGE_FOLLOWING_TEMPLATE_UNFOLLOW', 'takibi bırak');
define('LANG_PAGE_FOLLOWING_TEMPLATE_FOLLOWING', 'takip ediliyor');
define('LANG_PAGE_FOLLOWING_TEMPLATE_SEARCH', 'Ara');
define('LANG_PAGE_FOLLOWING_TEMPLATE_PEOPLE_YOU_MIGHT_KNOW', 'Takip etmek isteyebileceğiniz kişiler');

#PAGE TEMPLATE - ABOUT US
define('LANG_PAGE_ABOUT_US_DISCOVER_EVENTS', 'Yeni Etkinlikler Keşfedin');
define('LANG_PAGE_ABOUT_US_DISCOVER_EVENTS_TEXT', 'Beğenebileceğiniz etkinlikleri bulmada yardımcı oluyoruz');
define('LANG_PAGE_ABOUT_US_SHARE_EVENTS', 'Etkinlik paylaş');
define('LANG_PAGE_ABOUT_US_SHARE_EVENTS_TEXT', 'Etkinliğinizin keşfedilmesini garanti ediyoruz');
define('LANG_PAGE_ABOUT_US_SHARE_TRACK_PEOPLE', 'Sevdiğiniz insanları takip edin');
define('LANG_PAGE_ABOUT_US_SHARE_TRACK_PEOPLE_TEXT', 'Sevdiğiniz insanların neler yaptıklarını takip edin');

#UPDATE PROFILE
define('LANG_UPDATE_PROFILE_ERROR_USERNAME_EMPTY', 'Kullanıcı adı bos'); // 128 
define('LANG_UPDATE_PROFILE_ERROR_USERNAME_TAKEN', 'Kullanıcı adı alindi'); // 133 675
define('LANG_UPDATE_PROFILE_ERROR_ENTER_NAME', 'Lütfen adınızı giriniz'); // 141 
define('LANG_UPDATE_PROFILE_ERROR_BUSINESS_NAME','Lütfen kurum adınızı giriniz');  
define('LANG_UPDATE_PROFILE_ERROR_BUSINESS_NAME_MIN','kurum adı en az 2 karakter olmalıdır'); 
define('LANG_UPDATE_PROFILE_ERROR_ENTER_LASTNAME', 'Lütfen soyadınızı giriniz'); // 146 
define('LANG_UPDATE_PROFILE_ERROR_EMAIL_EMPTY', 'E-Posta boş'); // 151 
define('LANG_UPDATE_PROFILE_ERROR_EMAIL_NOTVALID', 'E-Posta geçersiz'); // 155 
define('LANG_UPDATE_PROFILE_ERROR_EMAIL_EXISTS', 'E-Posta alındı'); // 159 665
define('LANG_UPDATE_PROFILE_ERROR_BIRTHDAY_NOTVALID', 'Doğum tarihi geçersiz'); // 168 
define('LANG_UPDATE_PROFILE_ERROR_FOUNDED_DATE_NOTVALID','Kurluş tarihi geçersiz');
define('LANG_UPDATE_PROFILE_ERROR_ENTER_LOCATION', 'Oturduğunuz yeri giriniz'); // 174
define('LANG_UPDATE_PROFILE_ERROR_PASSWORD_NOTMATCH', 'Şifreler uyuşmuyor'); // 195 
define('LANG_UPDATE_PROFILE_ERROR_MINCHAR', 'En az 6 karakter kullanın'); // 200,205,213
define('LANG_UPDATE_PROFILE_ERROR_PASSWORD_INCORRECT', 'Şifre hatalı'); // 209 
define('LANG_UPDATE_PROFILE_TITLE', 'Timety | Profil Güncelle'); // 330
define('LANG_UPDATE_PROFILE_MSG_SUC_UPDATED', 'Güncellendi'); // 330
define('LANG_UPDATE_PROFILE_ERROR_DATE_NOTVALID', 'Geçerli tarih girin'); // 684
define('LANG_UPDATE_PROFILE_ERROR_PASSWORD_ERROR', 'Şifre hata'); // 704
define('LANG_UPDATE_PROFILE_EMAIL', 'E-Posta'); // 721
define('LANG_UPDATE_PROFILE_OLD_PASSWORD', 'Eski Şifre'); // 749 
define('LANG_UPDATE_PROFILE_NEW_PASSWORD', 'Yeni Şifre'); // 774
define('LANG_UPDATE_PROFILE_CONFIRM_PASSWORD', 'Şifre Doğrula'); // 798
define('LANG_UPDATE_PROFILE_GENDER', 'Cinsiyet'); // 824
define('LANG_UPDATE_PROFILE_GENDER_MALE', 'Erkek'); // 824
define('LANG_UPDATE_PROFILE_GENDER_FEMALE', 'Kadın'); // 824
define('LANG_UPDATE_PROFILE_SOCIAL', 'Sosyal Ağlar'); // 844
define('LANG_UPDATE_PROFILE_USERNAME', 'Kullanıcı adı'); // 926 934
define('LANG_UPDATE_PROFILE_NAME', 'Ad'); // 952
define('LANG_UPDATE_PROFILE_FIRST_NAME', 'Ad'); // 960
define('LANG_UPDATE_PROFILE_SURNAME', 'Soyad'); // 977
define('LANG_UPDATE_PROFILE_LAST_NAME', 'Soyad'); // 985
define('LANG_UPDATE_PROFILE_BIRTHDAY', 'Doğum Tarihi'); // 1001
define('LANG_UPDATE_PROFILE_FOUNDED_DATE', 'Kuruluş Tarihi'); // 1001
define('LANG_UPDATE_PROFILE_BIRTHDAY_DETAIL', '(gg.aa.yyyy)'); // 1005
define('LANG_UPDATE_PROFILE_PROFILE', 'Profil Resmi'); // 1032
define('LANG_UPDATE_PROFILE_SHORT_BIO', 'Kısa Bilgiler'); // 1041
define('LANG_UPDATE_PROFILE_ABOUT', 'Hakkında'); // 1049
define('LANG_UPDATE_PROFILE_LOCATION', 'Konum'); // 1072 1076
define('LANG_UPDATE_PROFILE_LOCATION_PLACEHOLDER','Hangi Şehirdesin');
define('LANG_UPDATE_PROFILE_WEB_SITE', 'İnternet Siteniz'); // 1102 1110
define('LANG_UPDATE_PROFILE_UPDATE', 'Güncelle'); // 1126
define('LANG_UPDATE_PROFILE_CANCEL', 'İptal'); // 1127 
define('LANG_UPDATE_PROFILE_IMAGE_UPLOAD_1', 'Profil resminiz soldaki gibi görüntülenecektir.'); // 1157
define('LANG_UPDATE_PROFILE_IMAGE_UPLOAD_2', 'Değişiklik yapabilmek için sarı kareyi yeniden boyutlandırabilir ve sürükleyebilirsiniz. Resminizden memnunsanız \'Kaydet\' butonuna tıklayınız.'); // 1161
define('LANG_UPDATE_PROFILE_SAVE', 'Kaydet'); // 1127
define('LANG_UPDATE_PROFILE_IMPORT_FROM', 'buradan yükle'); // 1127

#USERS PAGE
define('LANG_PAGE_USERS_CLICK_HERE_ADD_EVENT', 'Etkinlik eklemek için tıklayın');
define('LANG_PAGE_USERS_MY_TIMETY_TODAY', 'Bugün');
define('LANG_PAGE_USERS_FOLLOW', 'takip et');
define('LANG_PAGE_USERS_UNFOLLOW', 'takibi bırak');
define('LANG_PAGE_USERS_FOLLOWING', 'takip ediliyor');

#SUGGEST FRIEND
define('LANG_PAGE_SUGGEST_FRIEND_TITLE', 'Timety | Arkadaşlarınız');
define('LANG_PAGE_SUGGEST_FRIEND_FIND_FRIENDS', 'Arkadaşlarınızı bulun');
define('LANG_PAGE_SUGGEST_FRIEND_LOADING', 'Yükleniyor...');
define('LANG_PAGE_SUGGEST_FRIEND_PEOPLE_YOU_KNOW', 'Tanıdığınız insanlar');
define('LANG_PAGE_SUGGEST_FRIEND_FOLLOW', 'takip et');
define('LANG_PAGE_SUGGEST_FRIEND_UNFOLLOW', 'takibi bırak');
define('LANG_PAGE_SUGGEST_FRIEND_FOLLOWING', 'takip edilen');
define('LANG_PAGE_SUGGEST_FRIEND_PEOPLE_MIGHT_YOU_KNOW', 'Takip etmek isteyebilecekleriniz');
define('LANG_PAGE_SUGGEST_FRIEND_INVITE_PEOPLE', 'Davet et');
define('LANG_PAGE_SUGGEST_FRIEND_INVITE_PEOPLE_PLACEHOLDER', 'Davet et');
define('LANG_PAGE_SUGGEST_FRIEND_FINISH', 'Tamamla');

#SIGN IN
define('LANG_PAGE_SIGNIN_TITLE', 'Timety | Giriş');
define('LANG_PAGE_SIGNIN_USERNAME_EMPTY', 'Kullanıcı adı boş');
define('LANG_PAGE_SIGNIN_PASSWORD_EMPTY', 'Şifre boş');
define('LANG_PAGE_SIGNIN_USERNAME_OR_PASSWORD_WRONG', 'Kullanıcı veya şifre hatalı');
define('LANG_PAGE_SIGNIN_LOGIN_HEADER', 'Giriş');
define('LANG_PAGE_SIGNIN_LOGIN_GOOGLE', 'GOOGLE+ İLE GİRİŞ');
define('LANG_PAGE_SIGNIN_LOGIN_FACEBOOK', 'FACEBOOK İLE GİRİŞ');
define('LANG_PAGE_SIGNIN_LOGIN_TWITTER', 'TWITTER İLE GİRİŞ');
define('LANG_PAGE_SIGNIN_INPUT_USERNAME_PLACEHOLDER', 'Kullanıcı adı veya email');
define('LANG_PAGE_SIGNIN_INPUT_PASSWORD_PLACEHOLDER', 'Şifre');
define('LANG_PAGE_SIGNIN_INPUT_REMEMBER_ME', 'Beni hatırla');
define('LANG_PAGE_SIGNIN_BUTTON_LOGIN', 'Giriş');
define('LANG_PAGE_SIGNIN_BUTTON_FORGET_PASS', 'Şifremi unuttum');
define('LANG_PAGE_SIGNIN_BUTTON_ABOUT_US', 'Timety Hakkında');

#REMEMBER PASS
define('LANG_PAGE_REMEMBER_TITLE', 'Timety | Beni hatırla');
define('LANG_PAGE_REMEMBER_ERROR_INVALID_PARAM', 'Hatalı parametreler');
define('LANG_PAGE_REMEMBER_ERROR_INVALID_ENAIL', 'Hatalo E-Posta adresi');
define('LANG_PAGE_REMEMBER_ERROR_USER_NOT_FOUND', 'Kullanıcı bulunamadı');
define('LANG_PAGE_REMEMBER_ERROR_EMPTY_PASS', 'Şifre boş');
define('LANG_PAGE_REMEMBER_ERROR_EMPTY_REPASS', 'Şifre boş');
define('LANG_PAGE_REMEMBER_ERROR_PASSWORDS_NOTMATCH', 'Şifreler uyuşmuyor');
define('LANG_PAGE_REMEMBER_HEADER_FORGOT', 'Şifremi Unuttum');
define('LANG_PAGE_REMEMBER_INPUT_EMAIL_PLACEHOLDER', 'E-Posta');
define('LANG_PAGE_REMEMBER_INPUT_PASSWORD_PLACEHOLDER', 'Şifre');
define('LANG_PAGE_REMEMBER_INPUT_CONFIRM_PASSWORD_PLACEHOLDER', 'Şifrenizi tekrarlayın');
define('LANG_PAGE_REMEMBER_BUTTON_LOGIN', 'Şifreyi Değiştir');

#Personal Info
define('LANG_PAGE_PI_TITLE', 'Timety | Kişisel bilgileriniz');
define('LANG_PAGE_PI_ERROR_EMPTY_USERNAME', 'Kullanıcı adı boş olamaz');
define('LANG_PAGE_PI_ERROR_TAKEN_USERNAME', '"Kullanıcı alındı"');
define('LANG_PAGE_PI_ERROR_EMPTY_FIRST_NAME', 'Adınızı giriniz');
define('LANG_PAGE_PI_ERROR_EMPTY_LAST_NAME', 'Soyadınızı giriniz');
define('LANG_PAGE_PI_ERROR_NOT_VALID_EMAIL', 'E-Posta geçersiz');
define('LANG_PAGE_PI_ERROR_EMPTY_EMAIL', 'E-Posta boş');
define('LANG_PAGE_PI_ERROR_TAKEN_EMAIL', 'E-Posta sistemde kayıtlı');
define('LANG_PAGE_PI_ERROR_EMPTY_LOCATION', 'Lokasyonunuzu giriniz');
define('LANG_PAGE_PI_ERROR_EMPTY_PASSWORD', 'Şifrenizi giriniz');
define('LANG_PAGE_PI_ERROR_NOT_MATCH_PASSWORD', 'Şifreler uyuşmuyor');
define('LANG_PAGE_PI_ERROR', 'Hata oluştu. Detay :');
define('LANG_PAGE_PI_FORM_HEADER', 'Kişisel bilgileriniz');
define('LANG_PAGE_PI_INPUT_USERNAME_PLACEHOLDER', 'Kullanıcı adı');
define('LANG_PAGE_PI_INPUT_FIRST_NAME_PLACEHOLDER', 'Ad');
define('LANG_PAGE_PI_INPUT_LAST_NAME_PLACEHOLDER', 'Soyad');
define('LANG_PAGE_PI_INPUT_EMAIL_PLACEHOLDER', 'E-Posta');
define('LANG_PAGE_PI_INPUT_LOCATON_PLACEHOLDER', 'Lokasyon');
define('LANG_PAGE_PI_INPUT_PASSWORD_PLACEHOLDER', 'Şifre');
define('LANG_PAGE_PI_INPUT_REPASSWORD_PLACEHOLDER', 'Şifre tekrarı');
define('LANG_PAGE_PI_BUTTON_NEXT', 'Devam');
define('LANG_PAGE_PI_TERMS_SERVICE_HTML', 'Üyelik kayıdı oluşturarak Timety\'nin <a href="http://about.timety.com/terms-of-service/" target="_blank">Hizmet şartlarini</a> ve <a href="http://about.timety.com/privacy-policy/" target="_blank">Gizlilik politikasını</a> kabul ediyorum.');

#Add Interest
define('LANG_PAGE_ADD_LIKE_TITLE', 'Timety | Kişisel bilgileriniz');
define('LANG_PAGE_ADD_LIKE_FORM_HEADER1', 'Beğenileriniz nelerdir? ');
define('LANG_PAGE_ADD_LIKE_FORM_SELECT_COUNT_TEXT', 'En az 5 tane seçin.');
define('LANG_PAGE_ADD_LIKE_FORM_SELECT_COUNT_NUMBER', ' 4');
define('LANG_PAGE_ADD_LIKE_FORM_SELECT_ITEM_TEXT', ' adet');
define('LANG_PAGE_ADD_LIKE_FORM_SELECT_ITEM_S_TEXT', '');
define('LANG_PAGE_ADD_LIKE_FORM_SELECT_ITEM_REMAINING', ' kaldı.');
define('LANG_PAGE_ADD_LIKE_FORM_SELECT_ITEM_DONE', '');
define('LANG_PAGE_ADD_LIKE_FORM_SELECT_SUB_HEADER', ' İlgi alanlarınızı seçin. Timety\'i ziyaret ettiğinizde ilgili etkinlikleri size sunacağız');
define('LANG_PAGE_ADD_LIKE_FORM_BUTTON_FINISH', 'Bitir');

#login with twitter
define('LANG_PAGE_LOGIN_W_TW_ERROR', 'Birşeyler yanlış gitti. Detay:');

#get  google user 
define('LANG_PAGE_GET_GOOGLE_USER_TITLE', 'Timety | Google');
define('LANG_PAGE_GET_GOOGLE_USER_ERROR_TAKEN', 'Google hesabı mevcut!');
define('LANG_PAGE_GET_GOOGLE_USER_ERROR', 'Hata oluştu: ');
define('LANG_PAGE_GET_GOOGLE_USER_EMPT_USER', 'Kullanıcı boş');

#get fourquare user 
define('LANG_PAGE_GET_FOURSQUARE_USER_TITLE', 'Timety | Fourquare');
define('LANG_PAGE_GET_FOURSQUARE_USER_ERROR_TAKEN', 'Fourquare hesabı mevcut!');
define('LANG_PAGE_GET_FOURSQUARE_ERROR', 'Hata oluştu: ');
define('LANG_PAGE_GET_FOURSQUARE_USER_EMPTY_USER', 'Kullanıcı boş');

#get facebook user 
define('LANG_PAGE_GET_FACEBOOK_ERROR', 'Hata oluştu: ');

#forgot password
define('LANG_PAGE_FORGOT_PASS_TITLE', 'Timety | Şifremi unuttum');
define('LANG_PAGE_FORGOT_PASS_ERROR_INVALID_MAIL', 'Geçerli E-Posta giriniz');
define('LANG_PAGE_FORGOT_PASS_ERROR_USER_NOT_FOUND', 'Kullanıcı bulunamadı');
define('LANG_PAGE_FORGOT_PASS_EMAIL_SEND', 'Şifre hatırlatıcı E-Posta gönderildi');
define('LANG_PAGE_FORGOT_PASS_ERROR', 'Hata oluştu: ');
define('LANG_PAGE_FORGOT_PASS_HEADER', 'Şifremi unuttum');
define('LANG_PAGE_FORGOT_PASS_EMAIL_PLACEHOLDER', 'E-Posta');
define('LANG_PAGE_FORGOT_PASS_SNED_MAIL', 'E-Posta gönder');

#edit event
define('LANG_PAGE_EDIT_EVENT_TITLE', 'Timety | Etkinlik güncelle');
define('LANG_PAGE_EDIT_EVENT_SUC_UPDATED', 'Etkinlik güncellendi');
define('LANG_PAGE_EDIT_EVENT_ERROR', 'Hata oluştu:');
define('LANG_PAGE_EDIT_EVENT_BUTTON_UPDATE_EVENT', 'Etkinlik güncelle');
define('LANG_PAGE_EDIT_EVENT_ERROR_ON_DELETE', 'Etkinlik silinirken hata meydana geldi');
define('LANG_PAGE_EDIT_EVENT_DELETE_SUC', 'Etkinlik Sİlindi');

#create account 
define('LANG_PAGE_CREATE_ACCOUNT_TITLE', 'Timety | Üye olun');
define('LANG_PAGE_CREATE_ACCOUNT_FORM_HEADER', 'Hesap oluşturun');
define('LANG_PAGE_CREATE_ACCOUNT_SIGN_MAIL', '<p>veya email adresinizle <a onclick="analytics_createAccountButtonClicked(function(){window.location=\''.PAGE_ABOUT_YOU.'?new\';});"  style="cursor:pointer;color:#0000EE;">üye olunuz.</a></p><p/>Daha önce kayıt olduysanız <a href="'.PAGE_LOGIN.'">Giriş yapın</a></p>');
define('LANG_PAGE_CREATE_ACCOUNT_SIGN_UP_NOW','Şimdi Kayıt olun');
define('LANG_PAGE_CREATE_ACCOUNT_ABOUT_TIMETY', 'Timety Nedir');
define('LANG_PAGE_CREATE_ACCOUNT_BUSINESS', 'Kurumsal');
define('LANG_PAGE_CREATE_ACCOUNT_EXPLORE_HEADER', 'Keşfet');
define('LANG_PAGE_CREATE_ACCOUNT_EXPLORE_TEXT', 'Begenebileceginiz etkinlikleri bulmada yardimci oluyoruz');
define('LANG_PAGE_CREATE_ACCOUNT_SHARE_HEADER', 'Paylaş');
define('LANG_PAGE_CREATE_ACCOUNT_SHARE_TEXT', 'Etkinliginizin kesfedilmesini garanti ediyoruz');
define('LANG_PAGE_CREATE_ACCOUNT_TRACK_HEADER', 'Takip Et');
define('LANG_PAGE_CREATE_ACCOUNT_TRACK_TEXT', 'Sevdiginiz insanlarin neler yaptiklarini takip edin');
define('LANG_PAGE_CREATE_ACCOUNT_PRIVACY', 'Gizlilik');
#add facebook user
define('LANG_PAGE_ADD_FB_TITLE', 'Timety | Facebook');
define('LANG_PAGE_ADD_FB_ERROR', 'Hata oluştur ');
define('LANG_PAGE_ADD_FB_USER_TAKEN', 'Facebook hesabı mevcut!');
define('LANG_PAGE_ADD_FB_USER_EMPTY', 'Kullanıcı boş');

#add twitter user
define('LANG_PAGE_ADD_TW_TITLE', 'Timety | Twitter');
define('LANG_PAGE_ADD_TW_USER_TAKEN', 'Twitter hesabı mevcut!');
define('LANG_PAGE_ADD_TW_USER_EMPTY', 'Kullanıcı boş');

######################## AJAX - PAGES ########################
#GENERAL
define('LANG_AJAX_SECURITY_SESSION_ERROR','Kullanıcı giriş yapmadı');
define('LANG_AJAX_NO_RESULT','Sonuç bulunamadı');
define('LANG_AJAX_ERROR','Hata oluştu: ');
define('LANG_AJAX_INVALID_PARAMETER','Geçersiz parametreler');
define('LANG_AJAX_USER_NOT_FOUND','Kullanıcı bulunamadı');

#Token Input
define('LANG_TOKEN_INPUT_HINT_TEXT_TAG','Eklenecek etiketi arattırınız');
define('LANG_TOKEN_INPUT_HINT_TEXT_PEOPLE','Davet edilecek kişiyi arattırınız');
define('LANG_TOKEN_INPUT_NO_RESULT','Sonuç bulunamadı');
define('LANG_TOKEN_INPUT_SEARCHING','Aranıyor...');

#image handling
define('LANG_AJAX_IMG_HANDLING_TYPE_ERROR','Sadece <strong>{0}</strong> dosyaları geçerli<br />');
define('LANG_AJAX_IMG_HANDLING_MAX_SIZE','Resim boyutu {0}MB altında olmalı');
define('LANG_AJAX_IMG_HANDLING_SELECT_IMG', 'Yüklemek istediğiniz resmi seçiniz');

#invite email
define('LANG_AJAX_INVITE_MAIL_ERROR_SAME_USER','Kendinizi davet edemezsiniz');
define('LANG_AJAX_INVITE_MAIL_INVALID_EMAIL','Geçersiz E-Posta');
define('LANG_AJAX_INVITE_MAIL_INVALID_USER','Geçersiz Kullanıcı');

#notification
//0 if read display:none; 1 userUrl 2 user full name  3 event url  4 event title
define('LANG_AJAX_NOTIFICATION_COMMENTED', '<div style=\'line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;\'><img class=\'new_not\' src=\''. HOSTNAME .'images/new_not.png\' style=\'float: left;margin-top: 4px;margin-right: 5px;{0}\'><a onclick=\'window.location="{1}";\' style=\'color:#C2C2C2;float:left;cursor:pointer;\'>{2}</a>&nbsp;<span style=\'font-weight: normal;color:#C2C2C2;float:left;\'>&nbsp;yorum yaptı:&nbsp;</span><a  style=\'color:#C2C2C2;float:left;cursor:pointer;\' onclick=\'document.location="{3}"\'>\'{4}\'</a>');
//0 if read display:none; 1 userUrl 2 user full name  3 event url  4 event title
define('LANG_AJAX_NOTIFICATION_LIKED', '<div style=\'line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;\'><img class=\'new_not\' src=\''. HOSTNAME .'images/new_not.png\' style=\'float: left;margin-top: 4px;margin-right: 5px;{0}\'><img src=\''. HOSTNAME .'images/plus.png\' style=\'float: left;margin-top: 4px;margin-right: 5px;\'><a onclick=\'window.location="{1}";\' style=\'color:#C2C2C2;float:left;cursor:pointer;\'>{2}</a>&nbsp;<span style=\'font-weight: normal;color:#C2C2C2;float:left;\'>&nbsp;beğendi:&nbsp;</span><a  style=\'color:#C2C2C2;float:left;cursor:pointer;\' onclick=\'document.location="{3}";\'>\'{4}\'</a>');
//0 if read display:none; 1 userUrl 2 user full name  3 event url  4 event title
define('LANG_AJAX_NOTIFICATION_JOIN', '<div style=\'line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;\'><img class=\'new_not\' src=\''. HOSTNAME .'images/new_not.png\' style=\'float: left;margin-top: 4px;margin-right: 5px;{0}\'><img src=\''. HOSTNAME .'images/people.png\' style=\'float: left;margin-top: 4px;margin-right: 5px;\'><a onclick=\'window.location="{1}";\' style=\'color:#C2C2C2;float:left;cursor:pointer;\'>{2}</a>&nbsp;<span style=\'font-weight: normal;color:#C2C2C2;float:left;\'>&nbsp;katıldı:&nbsp;</span><a  style=\'color:#C2C2C2;float:left;cursor:pointer;\' onclick=\'document.location="{3}";\'>\'{4}\'</a>');
//0 if read display:none; 1 userUrl 2 user full name  3 event url  4 event title
define('LANG_AJAX_NOTIFICATION_MAYBE', '<div style=\'line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;\'><img class=\'new_not\' src=\''. HOSTNAME .'images/new_not.png\' style=\'float: left;margin-top: 4px;margin-right: 5px;{0}\'><img src=\''. HOSTNAME .'images/people.png\' style=\'float: left;margin-top: 4px;margin-right: 5px;\'><a onclick=\'window.location="{1}";\' style=\'color:#C2C2C2;float:left;cursor:pointer;\'>{2}</a>&nbsp;<span style=\'font-weight: normal;color:#C2C2C2;float:left;\'>&nbsp;katılabilir:&nbsp;</span><a  style=\'color:#C2C2C2;float:left;cursor:pointer;\' onclick=\'document.location="{3}";\'>\'{4}\'</a>');
//0 if read display:none; 1 userUrl 2 user full name  3 event url  4 event title
define('LANG_AJAX_NOTIFICATION_RESHARED', '<div style=\'line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;\'><img class=\'new_not\' src=\''. HOSTNAME .'images/new_not.png\' style=\'float: left;margin-top: 4px;margin-right: 5px;{0}\'><img src=\''. HOSTNAME .'images/people.png\' style=\'float: left;margin-top: 4px;margin-right: 5px;\'><a onclick=\'window.location="{1}";\' style=\'color:#C2C2C2;float:left;cursor:pointer;\'>{2}</a>&nbsp;<span style=\'font-weight: normal;color:#C2C2C2;float:left;\'>&nbsp;paylaştı:&nbsp;</span><a  style=\'color:#C2C2C2;float:left;cursor:pointer;\' onclick=\'document.location="{3}";\'>\'{4}\'</a>');
//0 if read display:none; 1 userUrl 2 user full name
define('LANG_AJAX_NOTIFICATION_FOLLOWED', '<div style=\'line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;\'><img class=\'new_not\' src=\''.HOSTNAME .'images/new_not.png\' style=\'float: left;margin-top: 4px;margin-right: 5px;{0}\'><img src=\''.HOSTNAME.'images/people.png\' style=\'float: left;margin-top: 4px;margin-right: 5px;\'><a onclick=\'window.location="{1}";\' style=\'color:#C2C2C2;float:left;cursor:pointer;\'>{2}</a>&nbsp;<span style=\'font-weight: normal;color:#C2C2C2;float:left;\'>&nbsp;seni takip etmeye başladı&nbsp;</span>');
//0 userUrl 1 user full name 2 event url  3 event title
define('LANG_AJAX_NOTIFICATION_INVITE_NEW_1', "<div style='line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;'><img class='new_not' src='" . HOSTNAME . "images/new_not.png' style='float: left;margin-top: 4px;margin-right: 5px;'><img src='" . HOSTNAME . "images/people.png' style='float: left;margin-top: 4px;margin-right: 5px;'><a onclick='window.location=\"{0}\";' style='color:#C2C2C2;float:left;cursor:pointer;'>{1}</a>&nbsp;<span style='font-weight: normal;color:#C2C2C2;float:left;'>&nbsp;seni katılmaya davet etti:&nbsp;</span><a  style='color:#C2C2C2;float:left;cursor:pointer;' onclick='document.location=\"{2}\"'>'{3}'</a>");
//0  res->getId() 1 res userId  2 res getNotEventId 
define('LANG_AJAX_NOTIFICATION_INVITE_NEW_2', "<br class='notf_answer_class'/><a class='notf_answer_class' style='color:#C2C2C2;float:left;cursor:pointer' onclick='return responseEvent({0},{1},{2},1);'>Katıl |&nbsp;</a><a class='notf_answer_class' style='color:#C2C2C2;float:left;cursor:pointer' onclick='return responseEvent({0},{1},{2},2);'>Belki |&nbsp;</a><a class='notf_answer_class' style='color:#C2C2C2;left:right;cursor:pointer' onclick='return responseEvent({0},{1},{2},3);'>Önemseme &nbsp;</a>");
//0 userUrl  1 user full name 2 event url 3 event title 4 response
define('LANG_AJAX_NOTIFICATION_INVITE_OLD', "<div style='line-height:18px;padding-top: 9px;height: auto;padding-bottom: 9px;display: table;'><img src='" . HOSTNAME . "images/people.png' style='float: left;margin-top: 4px;margin-right: 5px;'><a onclick='window.location=\"{0}\";' style='color:#C2C2C2;float:left;cursor:pointer;'>{1}</a>&nbsp;<span style='font-weight: normal;color:#C2C2C2;float:left;'>&nbsp;seni katılmaya davet etti:&nbsp;</span><a  style='color:#C2C2C2;float:left;cursor:pointer;' onclick='document.location=\"{2}\"'>'{3}'</a><span style='font-weight: normal;color:#C2C2C2;float:left;'>&nbsp;({4})&nbsp;</span>");

#UPLOAD IMAGE
define('LANG_AJAX_UPLOAD_IMAGE_CONT_LENGTH_NOT_SUPPORTED','Getting content length is not supported.');
define('LANG_AJAX_UPLOAD_IMAGE_MAX_POST_SIZE','{\'error\':\'increase post_max_size and upload_max_filesize to {0}\'}');
define('LANG_AJAX_UPLOAD_IMAGE_DIRECTORY_PERMISSION_ERROR','Server error. Upload directory isn\'t writable.');
define('LANG_AJAX_UPLOAD_IMAGE_NO_FILE_ERROR','Resim yüklenemedi.');
define('LANG_AJAX_UPLOAD_IMAGE_FILE_EMPTY_ERROR','Dosya boş');
define('LANG_AJAX_UPLOAD_IMAGE_FILE_TOO_LARGE_ERROR','Dosya çok büyük');
define('LANG_AJAX_UPLOAD_IMAGE_INVALID_EXT','Dosya uzantısı hatalı. {0} bunlardan biri olmalı.');
define('LANG_AJAX_UPLOAD_IMAGE_CANCELED_ERROR','Resim kaydedilemedi. Sistemde hata oluştu veya resim yükleme iptal edildi');

######################## Utils ########################
#general
define('LANG_UTILS_GENERAL_CONNECTION_ERROR','Bağlantı hatası: ');

#event fucntions
define('LANG_UTILS_EVENT_FUNCTIONS_ACTIVITY_CREATED','oluşturdu');
define('LANG_UTILS_EVENT_FUNCTIONS_ACTIVITY_JOINED','katılıyor');
define('LANG_UTILS_EVENT_FUNCTIONS_ACTIVITY_LIKED','beğendi');
define('LANG_UTILS_EVENT_FUNCTIONS_ACTIVITY_RESHARED','paylaştı');
define('LANG_UTILS_EVENT_FUNCTIONS_ACTIVITY_FOLLOWED','takip ediyor');

#functions
define('LANG_UTILS_FUNCTIONS_NEXT_MONTH','Gelecek ay');
define('LANG_UTILS_FUNCTIONS_MONTHS','{0} ay');
define('LANG_UTILS_FUNCTIONS_TOMORROW','Yarın');
define('LANG_UTILS_FUNCTIONS_NEXT_WEEK','Haftaya');
define('LANG_UTILS_FUNCTIONS_N_WEEKS','{0} hafta');
define('LANG_UTILS_FUNCTIONS_HOURS','{0} saat');
define('LANG_UTILS_FUNCTIONS_MINUTES','{0} dakika');
define('LANG_UTILS_FUNCTIONS_PAST','Geçmiş');

#Mail Functions
define('LANG_UTILS_MAIL_ERROR_EMAIL_EMPTY','E-Posta adresi boş');
define('LANG_UTILS_MAIL_ERROR_FILE_NOT_FOUND','Dosya bulunamadı');

#Neo4j Social Functions
define('LANG_UTILS_NEO4J_SOCIAL_ERROR_USER_NOT_FOUND','Kullanıcı bulunamadı');


######################## MAIL ########################
define('LANG_MAIL_INVITE_SUBJECT','Timety\'ye Davet');
define('LANG_MAIL_RESET_PASS_SUBJECT','Timety - Şifrenizi güncelleyin');
define('LANG_MAIL_INVITE_EVENT_SUBJECT','Timety Etkinlik Daveti');
define('LANG_MAIL_FOLLOWED_BY_SUBJECT','Timety\'de Yeni Bir Takipçiniz Var!');

#Language
define('LANG_TR_TR_TEXT', 'Türkçe');
define('LANG_EN_US_TEXT', 'İngilizce');
define('LANG_SELECT_LANGUAGE', 'Dil Seçiniz');
define('LANG_LANGUAGE', 'Diliniz');

#Create Business
define('LANG_PAGE_TITLE_BUSINESS', 'Timety | Create Business Account');
define('LANG_PAGE_BUSINESS_HEADER', 'Kurum Bilgileri');
define('LANG_PAGE_BUSINESS_NAME_PLACEHOLDER', 'Kurum Adı');
define('LANG_PAGE_BUSINESS_CONTACT_FIRST_NAME_PLACEHOLDER', 'İlgili kişi adı');
define('LANG_PAGE_BUSINESS_CONTACT_LAST_NAME_PLACEHOLDER', 'İlgili kişi soyadı');
define('LANG_PAGE_BUSINESS_BUSINESSNAME_ERROR_MIN', 'en az 2 karakter olmalıdır');
define('LANG_PAGE_BUSINESS_CONTACT_FIRST_NAME_ERROR', 'en az 3 karakter olmalıdır');
define('LANG_PAGE_BUSINESS_CONTACT_LAST_NAME_ERROR', 'en az 3 karakter olmalıdır');

#Error Pages
define('LANG_404_PAGE_TITLE', 'Sayfa Bulunamadı');
define('LANG_404_PAGE_CONTEXT', 'Sayfa Bulunamadı');
define('LANG_ERROR_PAGE_TITLE', 'Hata meydana geldi.');
define('LANG_ERROR_PAGE_CONTEXT', 'Hata meydana geldi.En kısa zamanda hata ile ilgileneceğiz.<br/>Ana sayfaya yönlendireleceksiniz.');
define('LANG_ERROR_PAGE_SEND_MAIL', 'Mail Gönder');
define('LANG_ERROR_PAGE_ALL_RIGHTS', 'Tüm hakları saklıdır');

#MAIN_ EVENTS
define('LANG_SOCIAL_JOIN', 'Katıl');
define('LANG_SOCIAL_MAYBE', 'Belki');
define('LANG_SOCIAL_EDIT', 'Düzenle');
define('LANG_SOCIAL_JOINED', 'Gidiyorsun');

#LANG
define('LANG_LOCALE', 'tr_TR');

#PAGE_TITLE
define('LANG_PAGE_TITLE_EVENTS','Etkinlikler');
define('LANG_PAGE_TITLE_ALL_EVENTS','Tüm Etkinlikler');
define('LANG_PAGE_TITLE_FOLOOWING_EVENTS','Takip Etkimlerimin Etkinlikleri');
define('LANG_PAGE_TITLE_EVENTS_FOR_TODAY','Büugün için Etkinlikler');
define('LANG_PAGE_TITLE_EVENTS_FOR_NEXT_7_DAYS','Önümüzdeki 7 Gün için Etkinlikler');
define('LANG_PAGE_TITLE_EVENTS_FOR_NEXT_30_DAYS','Önümüzdeki 30 Gün için Etkinlikler');
define('LANG_PAGE_TITLE_EVENTS_FOR_TOMORROW','Yarın için Etkinlikler');
define('LANG_PAGE_TITLE_EVENTS_FOR_THIS_WEEKEND','Bu Haftasonu için Etkinlikler');
define('LANG_PAGE_TITLE_EVENTS_FOR_YOU','Size Özel Etkinlikler');

#PAGE_DESCRIPTION
define('LANG_PAGE_DESC_ALL_INDEX','Asla Kaçırma! Şimdi sevdiğiniz etkinlikleri keşfetme, arkadaşlarınız ile paylaşma ve eğlenme zamanı. Etkinlikler artık her zamankinden daha iyi.');
define('LANG_PAGE_DESC_ALL_EVENTS','All events-  Şimdi sevdiğiniz etkinlikleri keşfetme, arkadaşlarınız ile paylaşma ve eğlenme zamanı. Etkinlikler artık her zamankinden daha iyi.');
define('LANG_PAGE_DESC_FOLLOWING_EVENTS','Takip Etkimlerimin Etkinlikleri-  Şimdi sevdiğiniz etkinlikleri keşfetme, arkadaşlarınız ile paylaşma ve eğlenme zamanı. Etkinlikler artık her zamankinden daha iyi.');
define('LANG_PAGE_DESC_EVENTS_FOR_TODAY','Bugünki etkinlikler -  Şimdi sevdiğiniz etkinlikleri keşfetme, arkadaşlarınız ile paylaşma ve eğlenme zamanı. Etkinlikler artık her zamankinden daha iyi.');
define('LANG_PAGE_DESC_EVENTS_FOR_TOMORROW','Yarınki etkinlikler -  Şimdi sevdiğiniz etkinlikleri keşfetme, arkadaşlarınız ile paylaşma ve eğlenme zamanı. Etkinlikler artık her zamankinden daha iyi.');
define('LANG_PAGE_DESC_EVENTS_FOR_THIS_WEEKEND','Bu haftasonundaki etkinlikler -  Şimdi sevdiğiniz etkinlikleri keşfetme, arkadaşlarınız ile paylaşma ve eğlenme zamanı. Etkinlikler artık her zamankinden daha iyi.');
define('LANG_PAGE_DESC_EVENTS_FOR_YOU','Seveceğiniz etkinlikler -  Şimdi sevdiğiniz etkinlikleri keşfetme, arkadaşlarınız ile paylaşma ve eğlenme zamanı. Etkinlikler artık her zamankinden daha iyi.');
define('LANG_PAGE_DESC_EVENTS_CATEGORY','{0} Etkilikleri -  Şimdi {1} etkinliklerini keşfetme, arkadaşlarınız ile paylaşma ve eğlenme zamanı. {0} etkinliklerini artık her zamankinden daha iyi.');
define('LANG_PAGE_DESC_EVENTS_FOR_NEXT_N_DAYS','Önümüzdeki {0} Dün içindeki Etkinlikler -  Şimdi sevdiğiniz etkinlikleri keşfetme, arkadaşlarınız ile paylaşma ve eğlenme zamanı. Etkinlikler artık her zamankinden daha iyi.');

#EVENT POPUP
define('LANG_MAIL_TEXT','Eposta');
define('LANG_POPUP_WEATHER','Hava Durumu');
define('LANG_POPUP_TICKET','Bilet');
define('LANG_POPUP_JOINING','Katılanlar');
define('LANG_POPUP_MAYBE','Belki');

define('LANG_GENERAL_OR','veya');
?>