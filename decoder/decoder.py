#!/usr/bin/env python
# Read in a WAV and find the freq's

import wave
import numpy as np
import sys


def check_symbol(num, freq):
    min_step = 500
    if freq > 3000:
        min_step = 2000
    if num < min_step:
        return "*"
    else:
        return "-"


def make_string(symbols_list):
    out_str = ""
    for symbol in symbols_list:
        out_str = out_str + symbol
    return out_str


def read_wav(fileName):
    waveFile = wave.open(fileName, 'rb')

    channels = waveFile.getnchannels()
    if channels != 1:
        print("Error 1: Only one channel in wav file!")
        exit(2)

    nframes = waveFile.getnframes()
    content = waveFile.readframes(nframes)
    samples = np.fromstring(content, dtype='u1')
    return samples


def get_points(a):
    '''
    max_dist
    slow: 3612 730 230
    medium: 2812 1212 389
    high: 1685 715 230
    '''
    max_dist = 0
    points = [0]
    i = 0
    minh = 120
    step = 1
    r = 0
    lena = len(a)
    while i < lena:
        while i < lena and a[i] < minh:
            i += step
        l = i
        while i < lena and a[i] > minh:
            i += step
        if i >= lena:
            break
        r = i

        dist = r - l
        if dist > 500:
            points.append(l)
            points.append(r)
            if dist > max_dist:
                max_dist = dist
        i += 1

    i = points[len(points) - 1]
    while i < lena:
        while i < lena and a[i] < minh:
            i += step
        while i < lena and a[i] > minh:
            i += step
        if i >= lena:
            break
        r = i
        i += 1
    points.append(r)
    return points, max_dist


def get_signals_list(points, freq):
    signal_list = []
    min_dist = 1000
    if 2000 < freq < 3000:
        min_dist = 2000
    elif freq > 3000:
        min_dist = 8000
    for i in range(1, len(points) - 1):
        if i % 2 == 0:
            dist = points[i] - points[i - 1]
            if dist > min_dist:
                signal_list.append(" ")
        else:
            dist = points[i] - points[i - 1]
            signal_list.append(check_symbol(dist, freq))
    dist = points[len(points) - 1] - points[len(points) - 2]
    signal_list.append(check_symbol(dist, freq))
    return signal_list


def decode_morse(fileName):
    a = read_wav(fileName)
    points, freq = get_points(a)
    signal_list = get_signals_list(points, freq)
    return  (make_string(signal_list))

if __name__ == "__main__":
    fileName = 'test.wav'
    if len(sys.argv) > 2:
        fileName = sys.argv[1]
    print(decode_morse(fileName))



