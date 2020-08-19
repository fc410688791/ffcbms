<?php
// $isSandboxTest = TRUE; // 开始沙箱模式:FALSE/TRUE
$isSandboxTest = FALSE; // 关闭沙箱模式
$config['isSandboxTest'] = $isSandboxTest;

if ( ! $isSandboxTest) {
    /*********************************************************正式环境 - start*/
    
    $config['gatewayUrl'] = 'https://openapi.alipay.com/gateway.do'; // 网关
    $config['appId'] = '2019060365504196'; // appid
    $config['seller_id'] = '2088531364764953'; // 卖家支付宝用户号(有的时候，一个商户可能有多个seller_id/seller_email)
    
    $config['rsaPrivateKey'] = 'MIIEpAIBAAKCAQEAv9yEjgF3PLTzzfsxivZ4kSess762kWd7lt4EJJeFXVT0Yzok
	2L9JquN35cRd1Lms1FZH5t3Lkm4lYS0WOH69dC4hvq0+D9Yp8Wk/vIVtgdtoiuxF
	eUpOR00XA/O04LE73156FlFiLwrhJkVEV9OVLZEW30g9b94GHopcID7+L/+G44Pj
	a959CnxLoSVBJ/J1FjX3nK0MMVY0dFHazlBNC+G5OCMBdPn7dfxMZL0XeVKb9Yc1
	kYIyUu4e6rEe/Vtwcy1+83w/GYR0N6F4BU05GVVBjdj8srysK3fLZroBxsm4c2sZ
	3CvMLOly4lDMlF6+d+6Uwj1RCWmyxZkq+8AmbQIDAQABAoIBAQCh8erZSpdw4HRi
	S0MYPs6qB0kZN0M3HJgPUjtA/7yMeScHko6DfYMbAYG0qblh04/0cxeh4VjxspNO
	oRX+fcxAgqftIKIgD++7W9F2uVEjqSVnDePea0ADoyhoJ49nkXIuKrr2l58+gfpl
	qVX0pB1pJuydb4acJFYZ9UOp3EIGFDxy5XVEPY0v3gWk/xuk2Q5LRZrjyALlgZC7
	FH1VdqNBFbuQx5uhq1M2y0AFcEqXQ9NLvjgwFm6m9K4VnP1+KBoe+wmkT9mE7J0p
	tg3BiPTz7bQZhDsVEkFd2ia8D6lSLLPLkKX5vMNfohkSj3Ac+Oa4rg5QWU/dIKcE
	OL7EkhCBAoGBAO9XLftvDLRZWQyzkVvOnVOnCZkcbHOdjinlrYMcy7IelhCbgbQO
	c11gszsjm8/NBnItT8lKocxchHnOQ3QExbMQbwdaXWCUyy05oU2TQ49gQeNdO//n
	2u5S0RRP6i8URlfCjh7dfqGhd129wp8he587m2j9p+flYV7jCq0w+adRAoGBAM03
	ThWg0xK4gj9P4xCsrP9tgf3ihhyLpyL2DuGvXu6Bjlnv40iQK+ilQPcKGErsfgU5
	fQ2QKMq/1evQI5JkZVA2D936IXFrXONDCEkQl3vinvN+xmmkLZRq9zLv3yvwuqSU
	TGa1nmX9z1tmGdwNp31gRRtuEHGw3O13zcuBTP5dAoGBAI5yJ4bWwAvyhWJe2St3
	3AD2kGrVFOL2qa7b6Jn5IG6hb5TwyJA+bYDs1Z9kAQ77KlP4Dd1ykruLcLkB/vll
	AbkiFcKdkKezxvziqNL/y5zgoDZOvi//cTiYh6F8MpfUjE0dGPLbxssxy1Y6Y0PM
	bMQHeOZS2YTV607K+Aa9+EgRAoGATrLv6hF0ANDrejY7wRBHg+lOGbw+bEIORbUR
	PfGQOW1L1yfNBO3tONnvEQ45BLqvFXfXglUPn7lz42w9yA1iYSFqcllTa/iddbYm
	PkbzoRnmlUC//JFuvWVMCae98U7vdOA+cIEkDr8IP2fncOF3ovhBaGSMP91wzEzO
	ikW67i0CgYBdyruWePSwIVDFvd+R/Z2WinodSjR6X7WygOA+HjTcidW9/mY04OCZ
	HAJs9uRP1llTt5d7Hx49tH3dSpfd92eD80MbbaJkxctxOg0fni/btw2mLqw+Punl
	OPsJGpzWn6yrH/G4aoXB4MlKbP4idjIPL4ap2iMSI6bLHKNvDrBYEQ=='; // 应用私钥
    
    $config['alipayrsaPublicKey'] = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqNMxVF/3gFx7cbUSM1rW/thk1m80DmNO9mWGBduaK4Blo8MGtfABMVv/+rSAt6+Mp06AbJb8wjmohRdlkdSEcfbrQEiCGFzwmApKjhey9NF4Kn38UGVFGgYb1JC8JlfXPyTJCVSh1aP7Q3BTLqtAsOxa2agVkIhtGf8z9p926NOf6Zu8ZxBPGFSL1GNpimIBIj/gLTMfo2lEyXRj/QvnAlhHPibgY8/9mQlyVXvzVcF6LFb7HYmaWoTEDTI8fAPB2U2AoMGWyLT5BHovQ65g6bBQls+/WCwh3YDo2cvsXEV2vVGSzN9Aun+8ICKYQftg8kw67U+BB7yelsJpwSzdzQIDAQAB'; // 支付宝公钥
    $config['signType'] = 'RSA2'; // 签名方式
    
    $config['aes'] = '3tlylAMhe/2yb/A6FqgdAw=='; // 加密方式：AES密钥
    
    /*********************************************************正式环境 - end*/
} else {
    /*++++++++++++++++++++++++++++++++++++++++++++++++++++++++沙箱环境 - start************************************************/
    $config['gatewayUrl'] = 'https://openapi.alipaydev.com/gateway.do'; // 网关
    $config['appId'] = '2016092900621562'; // appid
    $config['seller_id'] = '2088102177761257'; // 卖家支付宝用户号(有的时候，一个商户可能有多个seller_id/seller_email)
    
    $config['rsaPrivateKey'] = 'MIIEpQIBAAKCAQEAzqPbhye6yDuJiC9dCzwR1QXu2bSNlTYSXuL/ydw43kg/sCtT
	O5ruWOd1QcxgNs/KkF/V8At4bNJvTKQGlY9+jlfSwCum4/wLQLPGwPjThr8/uXCq
	Hb5gj3tXvHhGi5BDHAzBs4qSC+solff7l1ajdyf47qH3xs18lFD4dx/G3HbbMGqF
	lFdnbZYNehaCipi4VZ6KxuuVtkR2zyuCp15qLci1VyhQP2787CM67vk/1HhVF/ta
	en9PFg1sOJrTW8d8DBrsu5vBb1TsDrhSy+PHDEbOEvkJ20+vZ+fWog/1+jgLABFY
	SKdKZtodPWCfFBznFJfA8cHOOlM3XrWVIEJxbwIDAQABAoIBAQCw3OR/mMywpuy/
	KDLN89sbZhmaMoRgKsiseEQQZTIzZslEIsD5qlDIvUrKR4AuScmJqs00yMyI79Ad
	W+Lazde3Q4n5uyL7zXVgKr852X9KvJRxRvIDxTdmd16PzK4stAwMjQqZfkgkdWDm
	gzrOvHPq/8Z7kRu/B4/Sbj9LzmHpvTJ/6koOOcR4BI/Ek52+3qpo9DwBlSkU+dQ/
	YXzknfc7bYfBA8OzLjp91/lJ/mYZ+CPy1GNrFzYq/9LZHOV/RGOoHGtgG1rkpmZw
	0HjES6+gy6NVDoKT5PMflhL4HCKM2xDPi4RgWv/FzXKHod/TjOi/ua0rrHZWiYFy
	TU/InroRAoGBAPchIJ0a/zU+cskzDrgCtEyIcjj7MyqHfh+VQEdqLJRYS9Tin8TY
	Fc8qRfu7zcv+zgfAq8lvMa3P77WNE8PZ8gcEqELUiZFokagbPkcMgYj/09QLgqds
	SJ68C1wEpWOP9xkTq+7hDj9g2OHJPLN9fg1QHXRD6Ex5y/5/5eKl8m65AoGBANYO
	rG2UtKI1mHCg2Yx6gAPgDi4WzKyxC7JPgvuSp0S0z80URTJ2smh2NQqBfcsC+C9A
	ccMqrm/qNsBkLXJU/piVcU5V8R4yCAF2xRwDo3zNzfTYlCf6Ywt7/qQzKjNMxwz5
	omYYsj+lQmOIdOwD8t/TTJanJs4BPor9E0c8nI1nAoGBAK8OsGOZtB6rVfRgTpTa
	lY2BOihTCTOfNyB4QUhVVa7fvRfYUQTHbOuLLnfb2TQgEyI6wXW3ZqGRImSgwteB
	k4iVK/vrQmfWgxdp9PuvSDMbxZn3bV3bvrVyzzknsWCNoqQI40ob8lPC7t9CBdW/
	l4MmtTaew/cob/Cf+OBMXSdBAoGBAJzwGykoOpsmskH5HCbrdUniDmNqIduG3m13
	8C9j4TD1Y9kWwHj0H2+JIvA16jVaUv3JwN53P3cb/9JkYBGQES1re6wURHh/8/Cm
	1HLqlAWfmh2mYFFDOTSlTxexz4HXC6UOrSsvdhhFoHv7AsY7SukhMOjPWhr1PJaI
	X4b+AEWHAoGAF27+27Y3NPYPLGRL3o00Bsu+OuVvDF9PbVs0aUHUN4KYY4klRUFu
	e4kde6YVtSLvNc3ocwDMnPvUdZy8TgPwI/XPIxWMUWQDtQOsnYRLHOIrkAdbq61y
	am8ZGOPbvIVVOY4wE7SvMh+UFWc0CBqrgZzoEvDVm25eZxLNpQ5QDyI='; // 应用私钥
    
    $config['alipayrsaPublicKey'] = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0x5jP2cp5ZbYT0uwBMVDjSVha5UxDpz5IVPv8/VFW+cSsSutxC+iHZgGXsZppOD7noBIT1HOrsgv/JA8i1tlGc3wT7/Jzz2T3FjEDSvI4omRzZ/8GTE2F9owdG0UN0ACrwvlLDInAns3hsy1dbU/XkwFtuJE0+QDBn9tb+Tdfq9pkPRMi6kO8a4P41HUTXh+ImLrKGh8ll1Tep7FAwrKlKoavPC5qxaiepALrxzaJdw5HjxEmMMGeDgHt03f9fpX+PoChjt0IkSh19J8aG+lvy1tnRcb3eLMWFBYuK4HCmC35iiPR5NEdwNhtw2KhBZWX/SMHHhBXugtGpQFkR7gCwIDAQAB'; // 支付宝公钥
    $config['signType'] = 'RSA2'; // 签名方式
    
    /*++++++++++++++++++++++++++++++++++++++++++++++++++++++++沙箱环境 - end************************************************/
}

/*支付宝服务器主动通知商户服务器里指定的页面http/https路径。建议商户使用https*/
$config['ALIAPP_NOTIFY_URL'] = 'https://f.qkc88.cn/PayBack/aliappPayBack';
$config['ALIAPP_NOTIFY_URL_TEST'] = 'http://frp.qkc88.cn:8000/PayBack/aliappPayBack';