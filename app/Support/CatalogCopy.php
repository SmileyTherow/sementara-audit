<?php

namespace App\Support;

class CatalogCopy
{
    public static function commodity(string $name): ?string
    {
        $copy = [
            'cabai' => [
                'Lengkapi racikan dapur dengan cabai yang memberi sentuhan pedas pada hidangan favorit. Cocok untuk sambal ulek, bumbu balado, oseng, maupun kuah pedas yang menghangatkan suasana makan bersama. Sesuaikan jumlah cabai dengan selera agar rasa masakan tetap seimbang.',
                'Padukan dengan tomat untuk sambal pendamping lauk, atau iris tipis sebagai pelengkap tumisan praktis. Satu bahan serbaguna untuk banyak kreasi dapur rumahan. Tanyakan jenis cabai, jumlah yang tersedia, serta jadwal panennya melalui koperasi sebelum memesan.',
            ],
            'tomat' => [
                'Hadirkan warna cerah dan sentuhan rasa asam pada masakan dengan tomat yang mudah dipadukan dalam banyak resep. Dari sambal, sup, tumisan, hingga saus rumahan, tomat membantu membuat hidangan terasa lebih hidup tanpa racikan yang rumit.',
                'Potong sebagai pelengkap menu sehari-hari atau masak perlahan menjadi saus dan selai untuk kreasi dapur berikutnya. Satu bahan dengan banyak kemungkinan, cocok untuk Anda yang suka menyiapkan menu bervariasi. Hubungi koperasi untuk menyesuaikan jumlah pesanan dan mengetahui ketersediaan hasil panen.',
            ],
            'kangkung' => [
                'Menu sederhana pun terasa istimewa dengan sepiring kangkung hangat. Daun dan batangnya cocok diolah menjadi cah bawang putih, tumisan terasi, atau kangkung berbumbu cabai yang menggugah selera. Pas disandingkan dengan nasi hangat dan lauk favorit keluarga.',
                'Untuk hasil masakan yang masih bertekstur, masukkan kangkung menjelang akhir proses memasak. Selain menjadi sayur pendamping, daunnya bisa dikreasikan sebagai camilan berbalut tepung. Sampaikan kebutuhan Anda kepada koperasi untuk mendapatkan informasi jumlah tersedia dan jadwal panen yang sesuai.',
            ],
            'sawi' => [
                'Sawi hijau adalah pilihan praktis untuk menambah variasi menu dapur. Daunnya mudah dipadukan dengan bumbu sederhana, sementara batangnya memberi tekstur pada tumisan dan hidangan berkuah. Cocok untuk melengkapi mi, bakso, sup, maupun oseng sayur kesukaan keluarga.',
                'Masak bersama bawang putih untuk lauk pendamping yang ringkas, atau kombinasikan dengan tahu dan telur agar menu semakin beragam. Sawi juga dapat dimanfaatkan dalam adonan camilan sayur rumahan. Hubungi koperasi untuk mengetahui ketersediaan, satuan penjualan, dan jadwal panen sebelum memesan.',
            ],
            'jagung' => [
                'Dari camilan sore hingga sajian di meja makan, jagung selalu punya tempat dalam kreasi dapur. Biji-bijinya memberi tekstur khas pada sup, bakwan, dan olahan kukus, sementara tongkol utuh dapat direbus atau dibakar untuk dinikmati bersama keluarga.',
                'Sesuaikan jenis dan tingkat kematangan jagung dengan resep yang ingin dibuat. Jagung manis cocok untuk puding dan kudapan, sedangkan marning memerlukan jagung tua yang dikeringkan. Tanyakan pilihan hasil panen kepada koperasi agar pesanan sesuai dengan kebutuhan masakan Anda.',
            ],
            'terong' => [
                'Bawa cita rasa masakan rumahan ke meja makan dengan terong yang mudah menyerap bumbu. Teksturnya menjadi lembut setelah dimasak, cocok untuk balado, sambal terong, tumisan kecap, maupun terong bakar dengan sambal pendamping.',
                'Ingin variasi berbeda? Iris tipis dan balut dengan adonan tepung untuk membuat camilan gurih, atau padukan dengan cabai dan tomat menjadi lauk sederhana yang mengundang selera. Hubungi koperasi untuk mengetahui ukuran, jumlah tersedia, dan jadwal panen sesuai kebutuhan dapur Anda.',
            ],
            'selada' => [
                'Tambahkan kesan ringan dan tekstur renyah pada hidangan dengan selada. Daunnya cocok menjadi dasar salad, pelengkap burger dan roti isi, atau lalapan pendamping lauk berbumbu. Warna hijaunya membuat sajian sederhana tampak lebih menarik saat dihidangkan.',
                'Cuci dan tiriskan, lalu padukan dengan mentimun, wortel, atau tomat untuk kombinasi sayur yang beragam. Tambahkan saus pilihan menjelang waktu makan agar teksturnya tetap nyaman dinikmati. Tanyakan ketersediaan dan jadwal panen melalui koperasi untuk kebutuhan rumah tangga maupun sajian bersama.',
            ],
            'mentimun' => [
                'Mentimun memberi keseimbangan rasa pada hidangan yang gurih dan berbumbu. Teksturnya renyah dengan rasa ringan, pas untuk lalapan, acar, salad, atau pelengkap nasi dan lauk. Bahan sederhana yang membuat sajian sehari-hari terasa lebih lengkap.',
                'Iris tipis untuk pendamping makanan, potong dadu bersama wortel untuk acar rumahan, atau campurkan dalam kreasi sayur sesuai selera. Mentimun mudah digunakan tanpa proses memasak yang panjang. Hubungi koperasi untuk menanyakan jumlah tersedia, ukuran, dan jadwal panen sesuai kebutuhan Anda.',
            ],
            'wortel' => [
                'Warna jingga wortel membawa tampilan cerah dan rasa manis khas sayuran ke dalam masakan. Cocok untuk sup keluarga, capcai, tumisan, isian risol, maupun campuran acar. Teksturnya dapat disesuaikan dengan cara memotong dan lama memasak.',
                'Parut untuk adonan camilan, potong kecil sebagai campuran sayur, atau iris memanjang untuk pelengkap menu. Wortel memberi banyak pilihan olahan dari satu bahan sehingga dapur tidak terasa monoton. Tanyakan ketersediaan dan kebutuhan jumlah kepada koperasi sebelum menentukan pesanan Anda.',
            ],
            'bayam' => [
                'Semangkuk sayur bayam hangat menghadirkan rasa rumahan yang akrab. Daunnya cocok untuk sayur bening bersama jagung, tumisan sederhana, atau campuran hidangan berkuah yang menemani nasi dan lauk. Pilihan mudah saat ingin menyiapkan menu keluarga dengan bumbu yang tidak rumit.',
                'Daun bayam juga dapat dikreasikan menjadi keripik berbalut tepung untuk variasi camilan gurih. Pilih cara mengolah sesuai menu yang Anda rencanakan dan gunakan setelah dibersihkan. Hubungi koperasi untuk mengetahui jadwal panen serta jumlah yang tersedia bagi kebutuhan dapur Anda.',
            ],
            'buncis' => [
                'Buncis menghadirkan tekstur renyah yang menyenangkan pada berbagai hidangan sayur. Polong hijaunya cocok untuk tumisan bawang putih, sup, capcai, dan pelengkap lauk. Mudah dipadukan dengan wortel, jagung, atau bahan dapur lain untuk menu yang lebih berwarna.',
                'Potong serong untuk tumisan atau gunakan potongan pendek dalam hidangan berkuah. Sesuaikan waktu memasak dengan tekstur yang disukai, lalu sajikan bersama lauk pilihan. Untuk kebutuhan harian maupun memasak bersama keluarga, tanyakan ketersediaan hasil panen dan jumlah pesanan melalui koperasi.',
            ],
            'kol' => [
                'Kol menjadi andalan dapur karena mudah masuk ke banyak resep rumahan. Lembaran daunnya dapat diiris untuk sup dan tumisan, dijadikan campuran bakwan, atau diolah sebagai acar pendamping makanan. Rasanya yang ringan mudah menyatu dengan aneka bumbu.',
                'Gunakan irisan tipis untuk olahan yang cepat matang atau potongan lebih besar untuk hidangan berkuah. Padukan dengan wortel dan buncis agar warna serta tekstur sajian semakin beragam. Hubungi koperasi untuk informasi ukuran, ketersediaan, dan jadwal panen sebelum menyiapkan kebutuhan memasak Anda.',
            ],
        ];
        return isset($copy[mb_strtolower($name)]) ? implode("\n\n", $copy[mb_strtolower($name)]) : null;
    }

    public static function product(string $name): ?string
    {
        $key = mb_strtolower(trim(preg_replace('/\s+KWT.*$/iu', '', $name)));
        $copy = [
            'saus tomat' => [
                'Lengkapi hidangan favorit dengan saus tomat yang menghadirkan perpaduan rasa asam dan manis khas tomat. Cocok untuk cocolan gorengan, pelengkap telur dan mi, atau campuran bumbu saat menyiapkan menu keluarga. Pilihan praktis untuk memberi sentuhan rasa pada masakan sederhana.',
                'Sajikan di sisi piring atau gunakan sebagai dasar kreasi saus sesuai selera. Padukan dengan cabai bila ingin rasa lebih pedas, atau campurkan ke tumisan untuk variasi menu sehari-hari. Hubungi koperasi untuk informasi isi kemasan dan ketersediaan sebelum memesan.',
            ],
            'keripik pisang' => [
                'Nikmati momen santai dengan keripik pisang yang renyah dan mudah dinikmati kapan saja. Irisan pisang yang diolah menjadi camilan ini pas untuk teman minum teh, menemani percakapan keluarga, atau mengisi meja kudapan ketika ada tamu.',
                'Sajikan langsung dari kemasan atau tuangkan ke wadah kecil untuk berbagi. Cita rasa khas pisang menjadikannya pilihan camilan yang akrab bagi pencinta kudapan rumahan. Tanyakan varian rasa, isi kemasan, dan jumlah tersedia kepada koperasi agar pesanan sesuai dengan kebutuhan Anda.',
            ],
            'sambal olahan' => [
                'Buat waktu makan terasa lebih menggugah selera dengan sambal olahan sebagai pendamping lauk. Rasa pedasnya cocok menemani nasi hangat, tempe, tahu, telur, maupun aneka lauk goreng. Sedikit sambal memberi sentuhan berbeda pada menu rumahan yang sederhana.',
                'Nikmati sebagai cocolan atau campurkan secukupnya ke hidangan sesuai selera pedas Anda. Praktis untuk melengkapi sajian tanpa perlu meracik sambal dari awal setiap kali makan. Hubungi koperasi untuk mengetahui pilihan sambal, isi kemasan, dan ketersediaan pesanan.',
            ],
            'keripik bayam' => [
                'Kenali sisi lain bayam dalam camilan gurih yang renyah. Daun bayam dibalut adonan tepung tipis lalu digoreng, menghasilkan keripik dengan bentuk daun yang khas dan sensasi ringan saat dinikmati. Cocok untuk teman bersantai, pendamping makan, atau suguhan sederhana di rumah.',
                'Sajikan bersama teh hangat atau padukan dengan sambal favorit untuk pengalaman ngemil yang berbeda. Olahan sayur rumahan ini menjadi pilihan menarik saat ingin mencoba camilan selain keripik biasa. Hubungi koperasi untuk menanyakan isi kemasan, jumlah tersedia, dan kebutuhan pesanan Anda.',
            ],
            'keripik kangkung' => [
                'Kangkung tidak hanya nikmat ditumis. Daunnya juga bisa menjadi keripik gurih dengan balutan adonan berbumbu yang renyah. Bentuk daun dan batang tipisnya memberi karakter tersendiri pada camilan ini, cocok untuk Anda yang ingin mencoba variasi kudapan dari sayuran kebun.',
                'Nikmati sebagai camilan sore atau tambahkan di sisi piring sebagai pelengkap tekstur pada hidangan. Sajikan dalam wadah kecil untuk dinikmati bersama keluarga dan teman. Tanyakan isi kemasan serta ketersediaan kepada koperasi, lalu sesuaikan jumlah pesanan dengan kebutuhan Anda.',
            ],
            'keripik terong' => [
                'Terong hadir dalam bentuk camilan berbeda melalui irisan tipis berbalut adonan gurih. Digoreng menjadi keripik dengan tepian kulit ungu yang khas, olahan ini menawarkan cara baru menikmati bahan kebun yang biasanya menjadi lauk. Pas untuk variasi kudapan di rumah.',
                'Sajikan langsung sebagai teman bersantai atau padukan dengan saus dan sambal sesuai selera. Bentuk irisannya membuat keripik terong mudah dibagi sebagai suguhan keluarga. Hubungi koperasi untuk menanyakan isi kemasan, jumlah tersedia, dan kebutuhan pesanan sebelum menentukan jumlah yang diinginkan.',
            ],
            'stik wortel' => [
                'Stik wortel mengubah bahan sayur sehari-hari menjadi kudapan gurih yang menyenangkan. Wortel diparut, dicampur dalam adonan, lalu dibentuk menjadi stik dan digoreng hingga renyah. Warna jingganya memberi tampilan menarik pada camilan dengan bentuk yang praktis untuk dinikmati.',
                'Cocok menjadi teman minum teh, pengisi wadah camilan di rumah, atau suguhan saat berkumpul. Nikmati begitu saja atau tambahkan cocolan favorit untuk variasi rasa. Hubungi koperasi untuk informasi isi kemasan dan ketersediaan, termasuk kebutuhan beberapa bungkus untuk acara keluarga.',
            ],
            'stik sawi' => [
                'Coba olahan sawi yang berbeda dalam bentuk stik gurih untuk menemani waktu santai. Sawi hijau dipadukan dengan adonan, dibentuk memanjang, lalu digoreng menjadi camilan renyah. Pilihan sederhana yang menghadirkan bahan kebun dalam kreasi kudapan rumahan.',
                'Bentuk stiknya mudah disajikan dalam mangkuk kecil untuk berbagi bersama keluarga atau tamu. Nikmati langsung, atau padukan dengan saus pilihan sesuai selera. Tanyakan isi kemasan dan ketersediaan kepada koperasi agar jumlah pesanan sesuai dengan kebutuhan camilan harian maupun acara Anda.',
            ],
            'acar mentimun wortel' => [
                'Lengkapi lauk favorit dengan acar mentimun dan wortel yang menghadirkan rasa asam-manis serta tekstur renyah. Potongan sayurnya cocok menjadi penyeimbang hidangan gurih seperti nasi goreng, mi, lauk bakar, atau sajian berbumbu. Warna hijau dan jingganya ikut mempercantik piring makan.',
                'Sajikan secukupnya di sisi hidangan agar rasa utama dan acar saling melengkapi. Olahan rumahan ini dibuat sesuai pesanan, sehingga Anda dapat menanyakan kebutuhan wadah dan jumlah kepada koperasi. Cocok untuk melengkapi menu keluarga maupun kebutuhan sajian bersama.',
            ],
            'acar kol' => [
                'Acar kol menawarkan cara sederhana untuk memberi variasi pada menu makan. Irisan kol diolah dengan kuah acar bercita rasa asam-manis, menghadirkan tekstur sayur yang cocok menemani hidangan gurih dan berbumbu. Pas sebagai pelengkap nasi, lauk goreng, atau menu rumahan lainnya.',
                'Sajikan dalam porsi kecil untuk memberi keseimbangan rasa tanpa menutupi karakter lauk utama. Dibuat sesuai pesanan dan dikemas dalam wadah sederhana agar mudah disajikan. Hubungi koperasi untuk informasi ukuran wadah, jumlah pesanan, dan ketersediaan sebelum melengkapi menu pilihan Anda.',
            ],
            'selai tomat' => [
                'Temukan cara lain menikmati tomat melalui selai rumahan dengan perpaduan rasa manis dan sentuhan asam. Tomat matang dimasak perlahan bersama gula hingga menjadi olesan yang cocok untuk roti tawar, roti panggang, atau kreasi kudapan di rumah.',
                'Oleskan secukupnya untuk teman sarapan atau gunakan sebagai isian camilan sesuai resep favorit. Warna dan karakter rasa tomat memberi variasi menarik di antara pilihan selai yang biasa Anda nikmati. Tanyakan isi jar dan ketersediaan kepada koperasi untuk menyesuaikan kebutuhan pesanan.',
            ],
            'manisan tomat' => [
                'Tomat matang diolah dengan gula dan dikeringkan menjadi manisan dengan tekstur kenyal serta rasa manis yang berpadu dengan karakter asam tomat. Olahan rumahan ini menghadirkan pengalaman menikmati hasil kebun dalam bentuk kudapan yang berbeda dari buah segarnya.',
                'Nikmati sedikit demi sedikit saat bersantai atau sajikan sebagai variasi suguhan bersama teh. Potongannya mudah dituangkan ke wadah kecil untuk dinikmati bersama keluarga. Hubungi koperasi untuk mengetahui isi kemasan dan jumlah tersedia sebelum menentukan pesanan sesuai kebutuhan Anda.',
            ],
            'marning jagung' => [
                'Rasakan karakter camilan tradisional melalui marning jagung yang gurih dan renyah. Biji jagung tua dikeringkan, direbus, lalu digoreng dengan bumbu sederhana sehingga menghasilkan kudapan dengan tekstur yang khas. Cocok bagi Anda yang menyukai sensasi kriuk pada setiap suapan kecil.',
                'Sajikan sebagai teman ngobrol, pendamping minuman hangat, atau pengisi wadah camilan saat keluarga berkumpul. Nikmati dalam porsi kecil sesuai selera dan bagikan bersama orang terdekat. Tanyakan isi kemasan, pilihan bumbu, dan ketersediaan kepada koperasi sebelum memesan.',
            ],
            'puding jagung' => [
                'Hadirkan penutup makan dengan rasa manis dan aroma khas jagung melalui puding rumahan dalam cup. Jagung manis diparut lalu dimasak bersama bahan puding, menghasilkan kudapan lembut yang nyaman dinikmati saat bersantai atau setelah menu utama.',
                'Kemasan cup memudahkan penyajian per porsi untuk keluarga maupun acara kecil. Sajikan dingin bila menyukai sensasi yang lebih menyegarkan. Puding dibuat sesuai pesanan; hubungi koperasi untuk menanyakan ukuran cup, jumlah yang dibutuhkan, serta waktu pemesanan yang tersedia.',
            ],
            'sambal terong' => [
                'Lembutnya terong bertemu bumbu cabai dalam lauk rumahan yang menggugah selera. Potongan terong dimasak bersama sambal hingga bumbunya menyatu, menghadirkan perpaduan rasa pedas dan gurih yang cocok menemani nasi hangat, tahu, tempe, maupun lauk pilihan Anda.',
                'Sajikan sebagai lauk pendamping untuk memberi variasi pada menu sehari-hari tanpa perlu menyiapkan racikan dari awal. Dikemas dalam wadah sederhana dan dibuat sesuai pesanan. Hubungi koperasi untuk mengetahui ukuran porsi, ketersediaan, dan kebutuhan jumlah sesuai rencana makan bersama.',
            ],
        ];
        return isset($copy[$key]) ? implode("\n\n", $copy[$key]) : null;
    }
}
