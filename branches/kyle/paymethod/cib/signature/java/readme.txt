����˵��

һ��������嵥: 

���ܰ�   crypt.jar 
��������  cibsign.class(��Դ����) ����mac����
��֤����  cibverify.class(��Դ����)  У��mac����
sun��jce1.2.2��

����ʹ��˵��:

��jce/libĿ¼�µ��ĸ�.jar�ļ�������$java_home/jre/lib/extĿ¼
��CLASSPATH�а���crypt.jar

������ʹ��˵��

����

java cibsign kyle 12345678

����

kyle��Ҫ���ܵ���Ϣ
12345678 ����Կ

��֤

java cibverify 12345678 kyle A8EAB40E

����

12345678 ����Կ
kyle ��������Ϣ
A8EAB40E �����ļ��ܺ������
