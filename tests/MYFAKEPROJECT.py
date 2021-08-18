#!/bin/env python3
import sys, math, time

def main():
    c = check(sys.argv)
    f = create_f(c[0], c[1])
    iqs = compute_iq(f)
    if (len(c) == 2):
        print_iqs(iqs)
    elif (len(c) == 3):
        print_inf(compute_inferior(f, c[2]), c[2])
    elif (len(c) == 4):
        print_between(compute_between(f, c[2], c[3]), c[2], c[3])
    return 0

def check(av):
    parameters = []
    if "-h" in av:
        display_usage()
    if len(av) not in [3, 4, 5]:
        exit(84)
    for i in range (1, 3):
        try:
            if (float(av[i]) < 0 or float(av[i]) > 200):
                exit(84)
            parameters.insert(len(parameters), (float(av[i])))
        except:
            exit(84)
    if (parameters[1] == 0):
        exit(84)
    if (len(av) >= 4):
        try:
            if (int(av[3]) < 0 or int(av[3]) > 200):
                exit(84)
            parameters.insert(len(parameters), (int(av[3])))
        except:
            exit(84)
    if (len(av) == 5):
        try:
            if (int(av[4]) < 0 or int(av[4]) > 200):
                exit(84)
            parameters.insert(len(parameters), (int(av[4])))
            if (parameters[2] >= parameters[3]):
                exit(84)
        except:
            exit(84)
    return parameters

def display_usage():
    print("USAGE\n    ./205IQ u s [IQ1] [IQ2]\n")
    print("DESCRIPTION")
    print("    u       mean")
    print("    s       standard deviation")
    print("    IQ1     minimum IQ")
    print("    IQ2     maximum IQ")
    exit(84)

def create_f(u, s):
    def f(x):
        epow = (-1 / 2)
        epow *= ((x - u) / s) ** 2
        e = math.exp(epow)
        return e / (s * math.sqrt(2 * math.pi))
    return f

def compute_iq(f):
    iqs = []
    for x in range(0, 201):
        iq = f(x)
        iqs.insert(len(iqs), iq)
    return iqs

def print_iqs(iqs):
    for i in range(len(iqs)):
        print (f"{i} {iqs[i]:.5f}")

def compute_inferior(f, limit):
    return compute_between(f, 0, limit)

def compute_between(f, mini, maxi):
    surface = 0
    dt = 0.001
    x = mini
    while (x < maxi):
        surface += dt * (f(x) + f(x + dt)) / 2
        x += dt
    return surface

def print_inf(res, percent):
    print(f"{res*100:.1f}% of people have an IQ inferior to {percent}")
def print_between(res, a, b):
    print(f"{res*100:.1f}% of people have an IQ between {a} and {b}")

main()