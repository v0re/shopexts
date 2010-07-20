/*
* gcc  python_call.c  -fPIC -shared -o libpython_call.so
*/

int test(int a);
int testA(int a, int b);
char *testB(char *p);

typedef struct _AA
{
    int a;
    int b;
}AA, *PAA;

int testC(AA *p);
int testD(AA *p);

typedef struct _BB
{
    int a;
    char *pB;
    int c;
}BB, *PBB;

char *testE(BB *p);
int testF(BB *p);

int test(int a)
{
    return a;
}

int testA(int a, int b)
{
    return a + b;
}

char *testB(char *p)
{
    return p;
}

int testC(AA *p)
{
    return p->a + p->b;
}

int testD(AA *p)
{
    int tmp = p->a;
    p->a = p->b;
    p->b = tmp;
    return 0;
}

char *testE(BB *p)
{
    int tmp = p->a;
    p->a = p->c;
    p->c = tmp;
    return p->pB;
}

int testF(BB *p)
{
    int tmp = p->a;
    p->a = p->c;
    p->c = tmp;
    p->pB = "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
    return 0;
}