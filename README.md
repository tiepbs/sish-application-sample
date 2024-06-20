# sish-application-sample
Giới thiệu về một phiên bản open source rất tiện lợi, dễ dàng và miễn phí, đó là SISH (Ref: sish ) thay thế cho ngrok. 
# Background and Context
Trong công việc tìm hiểu, nghiên cứu, kiểm thử và phát triển hàng ngày, chúng ta có một số nhu cầu sau:

Thử nghiệm các API đang phát triển mà không cần triển khai chúng lên public server.

Tạo các kết nối an toàn giữa các ứng dụng chạy trên localhost với người dùng internet mà không cần qua VPN.

Thử nghiệm và chia sẻ ứng dụng, kết quả làm việc một cách dễ dàng hơn với đồng nghiệp hoặc khách hàng mà không cần đến môi trường triển khai phức tạp.

Tạo một public accessible service để nhận và xử lý một số nghiệp vụ với các third-party services để retrieve một số credentials, xem ví dụ cụ thể ở bên dưới Introduction to SISH - An open source version of NGROK and how to use it | Ví dụ cụ thể 

…

Với các use-cases này → chúng ta cần có một URL công khai, tạm thời hoặc lâu dài mà qua đó chúng ta có thể truy cập vào ứng dụng của chúng ta đang được host ở local từ xa qua internet. Và trước tới giờ, những khi có nhu cầu như thế này chúng ta hay sử dụng ngrok (Ref: ngrok | Unified Application Delivery Platform for Developers )

Tuy nhiên, hiện tại đã có một alternative open source rất tiện lợi, dễ dàng và miễn phí, đó là SISH (Ref: sish ) thay thế cho ngrok. 
# Giới Thiệu Qua Về SISH
## What:
 SISH là một giải pháp thay thế cho ngrok, tự lưu trữ (self-hosted), sử dụng custom domain (hiện tại JMango360 đang dùng custom domain là tunnel.jmango360.dev), cho phép chuyển tiếp cổng SSH để kết nối các ứng dụng localhost ra internet một cách an toàn. Ref: sish 

## Why: 
SISH cung cấp một phương pháp tiếp cận linh hoạt và an toàn cho các nhà phát triển muốn chia sẻ ứng dụng của mình trên internet mà không cần dựa vào dịch vụ bên ngoài như ngrok, giúp tăng cường quyền kiểm soát và bảo mật.

## How: 
SISH hoạt động bằng cách sử dụng Docker để triển khai dịch vụ chuyển tiếp cổng SSH trên máy chủ của người dùng, tạo các URL công khai để truy cập ứng dụng từ bất cứ đâu trên internet. Người dùng có thể thiết lập sish trên máy chủ riêng hoặc sử dụng dịch vụ được quản lý sẵn có.


# Hướng Dẫn Thiết Lập Khóa SSH Và Kết Nối Với SISH
Để sử dụng SSH kết nối với SISH một cách an toàn, bạn cần thiết lập cặp khóa SSH (private và public key) và đảm bảo rằng public key đã được thêm vào máy chủ SISH. Dưới đây là các bước cần thực hiện:

## Tạo cặp khóa SSH:
- Mở Terminal và chạy lệnh sau:
`ssh-keygen -t rsa -b 4096`

- Theo dõi các bước trên màn hình để tạo cặp khóa. Bạn có thể chọn lưu khóa tại vị trí mặc định và đặt mật khẩu cho khóa (nếu cần).

_Chú ý_: Những người đã có ssh access vào JMango360 Gitlab thì không cần làm thêm bước này vì devops team của chúng ta đã sync các SSH public keys từ Gitlab sang SISH server

## Thêm khóa công khai vào máy chủ SISH:
Sau khi tạo khóa => add vào máy chủ SISH:

`ssh-copy-id -i ~/.ssh/id_rsa.pub user@your-sish-server.com`
Thay user@your-sish-server.com bằng địa chỉ máy chủ và tài khoản người dùng tương ứng.

_Chú ý_: Những người đã có ssh access vào JMango360 Gitlab thì không cần làm thêm bước này vì devops team của chúng ta đã sync các SSH public keys từ Gitlab sang SISH server

## Kết nối đến SISH:
Một khi bạn đã có ssh access tới JMango360 gitlab hoặc thực hiện xong 2 bước trên, bây giờ bạn có thể kết nối đến máy chủ SISH mà không cần nhập mật khẩu:
`ssh - i -R <your_sub_domain>:<remote_port>:localhost:<local_port> your_custom_domain`

Ví dụ cụ thể: (nếu bạn chỉ có một cặp key or key mặc định của bạn là key đã được add vào SISH server thì không cần specify the private key khi gõ lệnh dưới)

```ssh -i ~/.ssh/id_rsa -R tbzoho:80:localhost:8000 tunnel.plchi.dev
/* Ở đây your_sub_domain = "tbzoho"
remote_port = 80
your_custom_domain = tunnel.jmango360.dev */
```
- Giải nghĩa:

Với câu lệnh này tôi đang dùng private key của tôi có tên là id_rsa để trong thư mục ~/.ssh để thực hiện tunnel map localhost:8000 trên máy tôi với https://tbzho.tunnel.plchi.dev

# Ví dụ cụ thể về ứng dụng của SISH
Giả sử bây giờ tôi có nhu cầu lấy access_token và refresh_token với các access scopes: scope = 'ZohoSubscriptions.products.CREATE,ZohoSubscriptions.subscriptions.UPDATE,ZohoSubscriptions.settings.READ,ZohoSubscriptions.addons.CREATE,ZohoSubscriptions.subscriptions.READ,ZohoSubscriptions.subscriptions.CREATE,ZohoCRM.modules.ALL,ZohoSubscriptions.customers.CREATE,ZohoSubscriptions.customers.READ,ZohoSubscriptions.hostedpages.READ,ZohoSubscriptions.plans.READ,ZohoSubscriptions.hostedpages.CREATE,ZohoCRM.modules.ALL,ZohoCRM.org.ALL,ZohoCRM.settings.ALL,ZohoCRM.users.ALL'... của Zoho organization XYZ, nhưng tôi không có quyền truy cập vào tài khoản admin backend. Mà chỉ có mr.B có access này, và vì nhiều lý do khác nhau mr.B không thể chia sẻ cho tôi access vào admin của Zoho XYZ org.

Khi đó, tôi cần gửi cho mr.B một public authorization URL để mr.B login vào Zoho org account và authorize, sau đó access_token và refresh_token sẽ được generate và show lên cho Frank, mr.B sẽ share lại cho tôi token này để tôi có thể tìm hiểu, nghiên cứu theo yêu cầu của công việc.

- Để làm được việc này, tôi cần làm như sau:
## Step #1: Hướng dẫn Fank vào Zoho admin console → tạo một Server-based Applicantion client
Sau khi tạo xong → Frank sẽ share với tôi Client ID và Client Secret của client này (việc này thực hiện rất đơn giản qua web interface của Zoho)

## Step #2: Tạo một PHP script đơn giản để xử lý quá trình xác thực và lấy token từ Zoho trên local host
Có thể tạo một đoạn PHP script như sau:
Check the `get_zoho_tokens.php` file

## Step #3: Chạy đoạn script trên ở local host
Chạy script trên ở local (nhớ thay thế client ID và client Secret thật vào) ở localhost:8000

`php -S localhost:8000`

## Step #4: Dùng SISH map localhost ở trên với một public URL để gửi cho mr.B
Chạy lệnh sau để map localhost:8000 trên máy của tôi ở trên với một public URL (trong trường hợp này là http(s)://tbzoho.tunnel.plchi.dev



`ssh -i ~/.ssh/id_rsa.pub -R tbzoho:80:localhost:8000 tunnel.plchi.dev`

## Step #5: Gửi cho mr.B public URL này để Frank authorize và generate access token và refresh token
Gửi cho mr.B public URL đầy đủ có trỏ đền file PHP trên

`https://tbzoho.tunnel.plchi.dev/get_zoho_tokens.php`

Sau khi click vào link trên và authorize thì Frank sẽ nhìn thấy được access token và refresh token mà chúng ta cần

mr.B sẽ nhận được screen để confirm to authorize -> sau đó sẽ thấy được access token và refresh token mà tôi cần có.


